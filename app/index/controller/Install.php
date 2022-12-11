<?php
// +----------------------------------------------------------------------
// | Description: 安装
// +----------------------------------------------------------------------
// | Author:  xiekunyu | raingad@foxmail.com 
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Db;
use think\facade\View;
use think\facade\Config;
use Env;

class Install extends BaseController
{
    // private $count = 100;
    // private $now = 0; 
    protected $status=1;

    public function _initialize()
    {
        /*防止跨域*/      
        header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
        $param = Request::instance()->param();          
        $this->param = $param;

//        $request = request();
//        $m = strtolower($request->module());
//        $c = strtolower($request->controller());
//        $a = strtolower($request->action());
//
//        if (!in_array($a, array('upgrade','upgradeprocess','checkversion')) && file_exists(CONF_PATH . "install.lock")) {
//            echo "<meta http-equiv='content-type' content='text/html; charset=UTF-8'> <script>alert('请勿重复安装!');location.href='".$_SERVER["HTTP_HOST"]."';</script>";
//            die();
//        }
    }

    /**
     * [index 安装步骤]
     * @author Michael_xu 
     * @param  
     */    
    public function index()
    {
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https';

        if (file_exists(CONF_PATH . "install.lock")) {
            echo "<meta http-equiv='content-type' content='text/html; charset=UTF-8'> <script>alert('请勿重复安装!');location.href='".$protocol."://".$_SERVER["HTTP_HOST"]."';</script>";
            die();     
        }

        if (!file_exists(PUBLIC_PATH . "sql/database.sql")) {
            echo "<meta http-equiv='content-type' content='text/html; charset=UTF-8'> <script>alert('缺少必要的数据库文件!');location.href='".$protocol."://".$_SERVER["HTTP_HOST"]."';</script>";
            die();     
        }

        return View::fetch('index');
    }

    // 检测环境配置和文件夹读写权限
    public function getEnv()
    {
        $data           = [];
        $data['env']    = self::checkEnv();
        $data['dir']    = self::checkDir();
        $data['version'] = $this->version();
        $data['status'] = $this->status;
        return success('',$data);
    }

    //版本
    public function version()
    {
        $res = include(CONF_PATH.'version.php'); 
        return $res ? : array('VERSION' => '0.5.18','RELEASE' => '20210518'); 
    }    

    // 检查数据库
    public function checkDatabase(){
         
        if (file_exists(CONF_PATH . "install.lock")) {
            return warning('请勿重复安装!');       
        } 
        if (!file_exists(PUBLIC_PATH . "sql/database.sql")) {
            return warning('缺少必要的数据库文件!');     
        } 
        $temp = $this->request->param();
        $db_config = $temp['form'];
        $db_config['type'] = 'mysql';      
        // $wkcode = $param['wkcode'];
        if (empty($db_config['hostname'])) {
            return warning('请填写数据库主机!');
        }           
        if (empty($db_config['hostport'])) {
            return warning('请填写数据库端口!');
        }
        if (preg_match('/[^0-9]/', $db_config['hostport'])) {
            return warning('数据库端口只能是数字!');
        }
        if (empty($db_config['database'])) {
            return warning('请填写数据库名!');
        }
        if (empty($db_config['username'])) {
            return warning('请填写数据库用户名!');
        }
        if (empty($db_config['password'])) {
            return warning('请填写数据库密码!');
        }        
        if (empty($db_config['prefix'])) {
            return warning('请填写表前缀!');
        }
        if (empty($db_config['redishost'])) {
            return warning('请填写redis主机地址!');
        }
        if (empty($db_config['redisport'])) {
            return warning('请填写redis端口!');
        }
        if (preg_match('/[^a-z0-9_]/i', $db_config['prefix'])) {
            return warning('表前缀只能包含数字、字母和下划线!');
        }
        
        // 创建数据库配置文件
        self::mkDatabase($db_config);
        // 检测数据库连接
        try{
            $conn=mysqli_connect($db_config['hostname'], $db_config['username'], $db_config['password']);
            // 检测连接
            if ($conn->connect_error) {
                return warning("连接失败: " . $conn->connect_error);
            }
            // 创建数据库
            $sql = "CREATE DATABASE IF NOT EXISTS `".$db_config['database']."` default collate utf8_general_ci ";
            if ($conn->query($sql) === TRUE) {
                return success('数据库连接成功',['status'=>1]);
            } else{
                return warning('没有找到您填写的数据库名且无法创建！请检查连接账号是否有创建数据库的权限!');
            }
        }catch(\Exception $e){
            return warning('数据库连接失败，请检查数据库配置！');
        }
        
    }

    // 执行安装
    public function install(){
        $db_config=Config::get('database.connections.mysql');
        $sql = file_get_contents( PUBLIC_PATH . "sql/database.sql");
        $sqlList = parse_sql($sql, 0, ['yu_' => $db_config['prefix']]);
        $install_count=0;
        if ($sqlList) {
            $sqlList = array_filter($sqlList);
            $install_count = count($sqlList);
            foreach ($sqlList as $k=>$v) {
                try {
                    $temp_sql = $v.';';
                    Db::query($temp_sql);
                } catch(\Exception $e) {
                    touch(CONF_PATH . "install.lock");
                    return error('数据库sql安装出错，请操作数据库手动导入sql文件'.$e->getMessage());
                }
            }
        } 
        touch(CONF_PATH . "install.lock");
        return success('安装成功',['status'=>$this->status],$install_count);
    }

	//ajax 进度条
    public function progress()
    {
        $data['length'] = session('install_count');
        $data['now'] = session('install_now');
        return success('',$data);
    }

        //添加database.php文件
        private function mkDatabase(array $data)
        {
            $code = <<<INFO
APP_DEBUG = true

[APP]
DEFAULT_TIMEZONE = Asia/Shanghai

[DATABASE]
TYPE = {$data['type']}
HOSTNAME = {$data['hostname']}
DATABASE = {$data['database']}
USERNAME = {$data['username']}
PASSWORD = {$data['password']}
HOSTPORT = {$data['hostport']}
CHARSET = utf8
DEBUG = true
prefix = {$data['prefix']}
[LANG]
default_lang = zh-cn

[REDIS]
HOST = {$data['redishost']}
PORT = {$data['redisport']}
PASSWORD ={$data['redispass']}

# 配置阿里云OSS，主要用于聊天文件储存
[OSS]
accesskeyid = 
accesskeysecret = 
endpoint = 
bucket = 
# 自有域名拼接，必须要有最后的/斜杠
ossurl = 

#配置预览功能，本系统主要使用第三方的预览工具，比如永中云转换，自带预览系统
[PREVIEW]
# 自带预览系统URL，主要用于预览媒体文件，已内置，必须要有最后的/斜杠
own = http://view.riangad.com/
# 永中云文件预览，主要用于文档预览，必须要有最后的/斜杠
yzdcs = 
# 永中云api code
keycode = 17444844212312
INFO;
    
            @file_put_contents( root_path().'.env', $code);
            $database=env('database.database');
            // 判断写入是否成功
            if (empty($database) || $database != $data['database']) {
                return warning('[.env]数据库配置写入失败！');
            }
            return true;
        }

    //添加database.php文件
    private function mkDatabase1(array $data)
    {
        $code = <<<INFO
<?php
return [
    // 自定义时间查询规则
    'time_query_rule' => [],

    // 自动写入时间戳字段
    // true为自动识别类型 false关闭
    // 字符串则明确指定时间字段类型 支持 int timestamp datetime date
    'auto_timestamp'  => true,

    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    'default'    =>    '{$data['type']}',
    'connections'    =>    [
        'mysql'    =>    [
            // 数据库类型
            'type'            =>env('database.type', '{$data['type']}'),
            // 服务器地址
            'hostname'        => env('database.hostname','{$data['hostname']}'),
            // 数据库名
            'database'        => env('database.database','{$data['database']}'),
            // 用户名
            'username'        => env('database.username','{$data['username']}'),
            // 密码
            'password'        => env('database.password','{$data['password']}'),
            // 端口
            'hostport'        => env('database.hostport','{$data['hostport']}'),
            // 数据库连接参数
            'params'            => [],
            // 数据库编码默认采用utf8
            'charset'           => env('database.charset', 'utf8'),
            // 数据库表前缀
            'prefix'            => env('database.prefix', '{$data['prefix']}'),

            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 是否严格检查字段是否存在
            'fields_strict'     => true,
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 监听SQL
            'trigger_sql'       => env('app_debug', true),
            // 开启字段缓存
            'fields_cache'      => false,
            // 字段缓存路径
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
            ]
        ]

];

INFO;
        file_put_contents( CONF_PATH.'database.php', $code);
        // 判断写入是否成功
        $config = include CONF_PATH.'database.php';
        if (empty($config['database']) || $config['database'] != $data['database']) {
            return warning('[config/database.php]数据库配置写入失败！');
        }
        return true;
    }

    //检查目录权限
    public function check_dir_iswritable($dir_path){ 
        $dir_path=str_replace( '\\','/',$dir_path); 
        $is_writale=1; 
        if (!is_dir($dir_path)) { 
            $is_writale=0; 
            return $is_writale; 
        } else { 
            $file_hd=@fopen($dir_path.'/test.txt','w'); 
            if (!$file_hd) { 
                @fclose($file_hd); 
                @unlink($dir_path.'/test.txt'); 
                $is_writale=0; 
                return $is_writale; 
            } 
            $dir_hd = opendir($dir_path); 
            while (false !== ($file=readdir($dir_hd))) { 
                if ($file != "." && $file != "..") { 
                    if (is_file($dir_path.'/'.$file)) { 
                        //文件不可写，直接返回 
                        if (!is_writable($dir_path.'/'.$file)) { 
                            return 0; 
                        }  
                    } else { 
                        $file_hd2=@fopen($dir_path.'/'.$file.'/test.txt','w'); 
                        if (!$file_hd2) { 
                            @fclose($file_hd2); 
                            @unlink($dir_path.'/'.$file.'/test.txt'); 
                            $is_writale=0; 
                            return $is_writale; 
                        } 
                        //递归 
                        $is_writale=$this->check_dir_iswritable($dir_path.'/'.$file); 
                    } 
                } 
            } 
        } 
        return $is_writale; 
    } 

    /**
     * [checkVersion 检查升级]
     * @author Michael_xu 
     * @param  
     */     
    public function checkVersion(){
        $version = Config::load('version');
        // $info = sendRequest($this->upgrade_site.'index.php?m=version&a=checkVersion', $version['VERSION']);
        // if ($info){
        //     return resultArray(['data' => $info]);
        // } else {
        //     return resultArray(['error' => '检查新版本出错!']);
        // }
    }    

    /**
     * 环境检测
     * @return array
     */
    private function checkEnv()
    {
        // $items = [
        //     'os'      => ['操作系统', PHP_OS, '类Unix', 'ok'],
        //     'php'     => ['PHP版本', PHP_VERSION, '7.3 ( <em style="color: #888; font-size: 12px;">>= 7.0</em> )', 'ok','性能更佳'],
        //     'gd'      => ['gd', '开启', '开启', 'ok'],
        //     'openssl' => ['openssl', '开启', '开启', 'ok'],
        //     'pdo' => ['pdo', '开启', '开启', 'ok'],
        // ];
        $items = [
            ['name'=>'操作系统','alias'=>'os','value'=>PHP_OS,'status'=> 'ok','description'=>"操作系统需要类Unix"],
            ['name'=>'PHP版本','alias'=>'version','value'=> PHP_VERSION,  'status'=>'ok','description'=>"PHP版本必须大于7.0"],
            ['name'=>'gd库','alias'=>'gd', 'value'=>'开启', 'status'=>'ok','description'=>"开启GD库"],
            ['name'=>'pdo','alias'=>'pdo', 'value'=>'开启', 'status'=>'ok','description'=>"PDO扩展"],
            ['name'=>'openssl','alias'=>'openssl', 'value'=>'开启',  'status'=>'ok','description'=>"OPENSSL扩展"],
            ['name'=>'pcntl','alias'=>'pcntl', 'value'=>'开启',  'status'=>'ok','description'=>"pcntl扩展，消息推送必须开启"],
            ['name'=>'posix','alias'=>'posix', 'value'=>'开启',  'status'=>'ok','description'=>"posix扩展，消息推送必须开启"],
            ['name'=>'event','alias'=>'event', 'value'=>'开启',  'status'=>'ok','description'=>"event选择安装,处理消息推送高并发"],
        ];
        foreach($items as $k=>$v){
            $status='ok';
            switch($v['alias']){
                case 'php':
                    if (substr($v['value'],0,3) < '7.0') {
                        $status='no';
                        $this->status=0;
                    }
                    break;
                case 'gd':
                    if (!extension_loaded('gd')) {
                        $items[$k]['value'] = '未开启';
                        $status='no';
                        $this->status=0;
                    }
                    break;
                case 'openssl':
                    if (!extension_loaded('openssl')) {
                        $items[$k]['value'] = '未开启';
                        $status='no';
                        $this->status=0;
                    }
                    break;
                case 'pdo':
                    if (!extension_loaded('pdo')) {
                        $this->status=0;
                        $items[$k]['value'] = '未开启';
                        $status='no';
                    }
                    break;
                case 'pcntl':
                    if (!extension_loaded('pcntl')) {
                        $this->status=0;
                        $items[$k]['value'] = '未开启';
                        $status='no';
                    }
                    break;
                case 'posix':
                    if (!extension_loaded('posix')) {
                        $this->status=0;
                        $items[$k]['value'] = '未开启';
                        $status='no';
                    }
                    break;
                case 'event':
                    if (!extension_loaded('event')) {
                        $items[$k]['value'] = '未开启';
                        $status='no';
                    }
                    break;
            }
            
            $items[$k]['status'] = $status;
        }
        return $items;
    }
    
    /**
     * 目录权限检查
     * @return array
     */
    private function checkDir()
    {
        $items = [
            ['dir', root_path().'app', 'app', '读写', '读写', 'ok'],
            ['dir', root_path().'extend', 'extend', '读写', '读写', 'ok'],
            ['dir', root_path().'runtime', './temp', '读写', '读写', 'ok'],
            ['dir', root_path().'public', './upload', '读写', '读写', 'ok'],
            ['file', root_path().'config', 'config', '读写', '读写', 'ok'],
        ];
        $items = [
            ['path'=>root_path().'app', 'dir'=>'app', 'value'=>'读写', 'type'=>'dir','status'=>'ok'],
            ['path'=>root_path().'extend', 'dir'=>'extend', 'value'=>'读写', 'type'=>'dir','status'=>'ok'],
            ['path'=> root_path().'runtime', 'dir'=>'runtime', 'value'=>'读写', 'type'=>'dir','status'=>'ok'],
            ['path'=>root_path().'public', 'dir'=>'public', 'value'=>'读写', 'type'=>'dir','status'=>'ok'],
            ['path'=>root_path().'config', 'dir'=>'config', 'value'=>'读写', 'type'=>'file','status'=>'ok'],
        ];
        $status=1;
        foreach ($items as $k=>$v) {
            if ($v['type'] == 'dir') {// 文件夹
                if (!is_writable($v['path'])) {
                    if (is_dir($v['path'])) {
                        $items[$k]['value'] = '不可写';
                        $items[$k]['status'] = 'no';
                    } else {
                        $items[$k]['value'] = '不存在';
                        $items[$k]['status'] = 'no';
                    }
                    $this->status=0;
                }
            } else {// 文件
                if (!is_writable($v['path'])) {
                    $items[$k]['value'] = '不可写';
                    $items[$k]['status'] = 'no';
                    $this->status=0;
                }
            }
        }
        return $items;
    }

    /**
     * 验证序列号
     * @param 
     * @return
     */        
    public function checkCodeOld($username) {
        $encryption = md5($username);
        $substr = substr($username, strlen($username)-6);
        $subArr = str_split($substr, 1);
        $code = '';
        for ($i = 0; $i <= 5; $i++) {
            $code .= $encryption[$subArr[$i]];
        }
        return $code;
    }

    //写入license文件
    private function mkLicense($wkcode)
    {
        file_put_contents( CONF_PATH.'license.dat', $wkcode);
        // 判断写入是否成功
        // $config = include CONF_PATH.'license.dat';
        // if (empty($config)) {
        //     return resultArray(['error' => 'license配置写入失败！']);
        // }
        return true;
    }    
}