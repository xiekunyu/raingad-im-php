<?php
// 应用公共文件
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\facade\Cache;
use GatewayClient\Gateway;
use \utils\Str;
/**
 * 框架内部默认ajax返回
 * @param string $msg      提示信息
 * @param string $redirect 重定向类型 current|parent|''
 * @param string $alert    父层弹框信息
 * @param bool $close      是否关闭当前层
 * @param string $url      重定向地址
 * @param string $data     附加数据
 * @param int $code        错误码
 * @param array $extend    扩展数据
 * @param int $count    总数
 */
function success($msg = '操作成功', $data = '', $count = 0, $page = 1, $code = 0)
{
    return ret($code, $msg, $data, $count, $page);
}

/**
 * 返回警告json信息
 */
function warning($msg = '操作失败', $data = '', $count = 0, $page = 1 , $code = 400)
{
    return success($msg, $data, $count, $page, $code);
}

/**
 * 返回错误json信息
 */
function error($msg = '操作失败', $code = 502)
{
    return ret($code, '系统错误：'.$msg);
}

/**
 * 提前终止信息
 */
function shutdown($msg = '禁止访问', $code = 401)
{
    exit(json_encode(['code' => $code, 'msg' => $msg, 'data' => []]));
}


/**
 * ajax数据返回，规范格式
 * @param array $data   返回的数据，默认空数组
 * @param string $msg   信息
 * @param int $code     错误码，0-未出现错误|其他出现错误
 * @param array $extend 扩展数据
 */
function ret($code, $msg = "",$data = [],$count=0, $page=0)
{
    $ret = ["code" =>$code, "msg" => $msg,'count'=>$count, "data" => $data,'page'=>$page];
    return json($ret);
}


/* @param string $string 原文或者密文
* @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
* @param string $key 密钥
* @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
* @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
*
* @example
*
*  $a = authcode('abc', 'ENCODE', 'key');
*  $b = authcode($a, 'DECODE', 'key');  // $b(abc)
*
*  $a = authcode('abc', 'ENCODE', 'key', 3600);
*  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {

    $ckey_length = 4;   
    // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥

    $key = md5($key ? $key : 'default_key'); //这里可以填写默认key值
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
     
    if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
    }
}

function ssoTokenEncode($str,$key='lvzhesso',$expire=0){
    $ids=encryptIds($str);
   return authcode($ids,"ENCODE",$key,$expire);
}

function ssoTokenDecode($str,$key='lvzhesso'){
    $ids=authcode($str,"DECODE",$key);
    try{
        return decryptIds($ids);
    }catch(\Exception $e){
        return '';
    }
}


//id加密
function encryptIds($str)
{
    $hash = config('hashids');
    return \Hashids\Hashids::instance($hash['length'], $hash['salt'])->encode($str);
}

//id解密
function decryptIds($str)
{
    $hash = config('hashids');
    return \Hashids\Hashids::instance($hash['length'], $hash['salt'])->decode($str);
}

/*
*第三方发送平台,包含阿里大于短信，极光推送，workerman，网页推送；
 * mobile,接收通知的号码
 * temp，数据库短信模板id
 * tempid，自定义选取对应的case，对应阿里设置的参数
*/
function sendsms($mobile, $temp, $tempid=1, $name = "律者")
{
    // 配置信息
    $conf = config('sms');
    AlibabaCloud::accessKeyClient($conf['app_key'], $conf['app_secret'])
        ->regionId('cn-hangzhou')
        ->asDefaultClient();
    $code = rand(1000, 9999);
    $param=[
        'code' => $code,
        'product' => $name
    ];
    try {
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "cn-hangzhou",
                    'PhoneNumbers' => $mobile,
                    'SignName' => $conf['app_sign'],
                    'TemplateCode' => $temp,
                    'TemplateParam' => json_encode($param),
                ],
            ])
            ->request();
        $res=$result->toArray();
        if($res['Code']=="OK"){
            $status = 1;
        }else{
            $status=0;
        }
        Cache::set($mobile,$code,300);
        return $status;
    } catch (ClientException $e) {
        return false;
    }
}

//密码生成规则
function password_hash_tp($password,$salt)
{
    return md5($salt.$password.$salt);
}

// 获取url中的主机名
function getHost($url){ 
    if(!preg_match('/http[s]:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){
        return '';
    }
    $search = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
    $url = trim($url);
    preg_match_all($search, $url ,$rr);
    return $rr[4][0];
}

//根据姓名画头像
function circleAvatar($str,$s,$uid=0,$is_save=0,$save_path=''){
    //定义输出为图像类型
    header("content-type:image/png");
    $str =$str?:"律者";
    $uid =$uid?:rand(0,10);
    $text=\utils\Str::getLastName($str,2);
    $width = $height = $s?:80;
    if($width<40 or $width>120){
        $width = $height =80;
    }
    $colors=['#F56C6C','#E6A23C','#fbbd08','#67C23A','#39b54a','#1cbbb4','#409EFF','#6739b6','#e239ff','#e03997'];
    $color=hex2rgb($colors[(int)$uid%10]);
    $size=$width/4;
    $textLeft=($height/2)-$size-$width/10;
    if($width<=80){
        $text=\utils\Str::getLastName($str,1);
        $size=$width/2;
        $textLeft=$size/3;
    }
//新建图象
    $pic=imagecreate($width,$height);
//定义黑白颜色
    $background=imagecolorallocate($pic,$color['r'],$color['g'],$color['b']);
    $textColor=imagecolorallocate($pic,255,255,255);
    imagefill($pic,0,0,$background);//填充背景色
//定义字体
    $font=root_path()."/public/static/fonts/PingFangHeavy.ttf";
    //写 TTF 文字到图中
    imagettftext($pic,$size,0,$textLeft,($height/2)+$size/2,$textColor,$font,$text);
    if($is_save){
        $path=$save_path."/".$uid.".png";
        $dir = pathinfo($path,PATHINFO_DIRNAME);
        if(!is_dir($dir)){
            $file_create_res = mkdir($dir,0777,true);
            if(!$file_create_res){
                return false;//没有创建成功
            }
        }
        imagepng($pic,$path);
        imagedestroy($pic);
        return $path;
    }else{
        //输出图象
        imagepng($pic);
        //结束图形，释放内存空间
        imagedestroy($pic);
        return $pic;
    }
}

//头像拼接
function avatarUrl($path, $str = "雨",$uid=0,$s=80)
{
    $str = Str::strFilter($str);
    if ($path) {
        // 判断头像路径中是否有http
        if (strpos($path, 'http') !== false) {
            $url = $path;
        } else {
            $url = getDiskUrl() .'/'. ltrim($path,'/') ;
        }
    }else {
        if($str){
            $url=request()->domain()."/avatar/".$str.'/'.$s.'/'.$uid;
        }else{
            $url='';
        }
    }
    return $url;
}

// 获取文件的地址
function getFileUrl($path){
    return getDiskUrl() .'/'. ltrim($path,'/') ;
}

/**
 * 十六进制 转 RGB
 */
function hex2rgb($hexColor)
{
    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
    } else {
        $color = $hexColor;
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        );
    }
    return $rgb;
}

/**
 * 将数组按字母A-Z排序
 * @return [type] [description]
 */
function chartSort($array, $field,$isGroup=true,$chart='chart')
{
    $newArray = [];
    foreach ($array as $k => &$v) {
        $v[$chart] = getFirstChart($v[$field]);
        $newArray[] = $v;
    }
    $data = [];
    if($isGroup){
        foreach ($newArray as $k => $v) {
            if (array_key_exists($v[$chart], $data)) {
                $data[$v[$chart]][] = $v;
            } else {
                $data[$v[$chart]] = [];
                $data[$v[$chart]][] = $v;
            }
        }
        ksort($data);
    }else{
       return $newArray;
    }
    return $data;
}

/**
 * 返回取汉字的第一个字的首字母
 * @param  [type] $str [string]
 * @return [type]      [strind]
 */
function getFirstChart($str)
{
    $str = str_replace(' ', '', $str);
    if (empty($str)) {
        return '#';
    }
    $char = ord($str[0]);
    if ($char >= ord('A') && $char <= ord('z')) {
        return strtoupper($str[0]);
    }
    $s1 = iconv('UTF-8', 'gb2312//IGNORE', $str);
    $s2 = iconv('gb2312', 'UTF-8//IGNORE', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return "#";
}

// 拼接聊天对象
function chat_identify($from_user,$to_user){
    $identify=[$from_user,$to_user];
    sort($identify);
    return implode('-',$identify);
}

//数组中获取ID字符串
function arrayToString($array,$field,$isStr=true){
    $idArr=[];
    foreach ($array as $k=>$v){
        $idArr[]=$v[$field];
    }
    $idArr=array_unique($idArr);
    if($isStr){
        $idStr=implode(',',$idArr);
        return $idStr;
    }else{
        return $idArr;
    }

}

// 根据文件后缀进行分类
function getFileType($ext,$rst=false){
    $ext=strtolower($ext);
    $image=['jpg','jpeg','png','bmp','gif'];
    $radio=['mp3','wav','wmv','amr'];
    $video=['mp4','3gp','avi','m2v','mkv','mov'];
    $doc=['ppt','pptx','doc','docx','xls','xlsx','pdf','txt','md'];
    $msgType='file';
    if(in_array($ext,$doc)){
        $fileType=1;
    }elseif(in_array($ext,$image)){
        $fileType=2;
        $msgType='image';
    }elseif(in_array($ext,$radio)){
        $fileType=3;
        $msgType='voice';
    }elseif(in_array($ext,$video)){
        $fileType=4;
        $msgType='video';
    }else{
        $fileType=9;
    }
    if($rst){
        return $msgType;
    }else{
        return $fileType;
    }
}

/**
 * 二位数组排序
 * $array 需要排序的数组
 * $sort_key 需要排序的字段
 * $sort_order 正序还是倒序
 * $sort_type  排序的类型:数字,字母
 */
function sortArray($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
{
    if (is_array($arrays)) {
        foreach ($arrays as $array) {
            if (is_array($array)) {
                $key_arrays[] = $array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
    return $arrays;
}

//gateway向web页面推送消息
function wsSendMsg($user, $type,  $data, $isGroup=0)
{
    $message = json_encode([
        'type' => $type,
        'time' => time(),
        'data' => $data
    ]);
    if (!$user) {
        Gateway::sendToAll($message);
    } else {
        if (!$isGroup) {
            $send = 'sendToUid';
        } else {
            $send = "sendToGroup";
        }
        Gateway::$send($user, $message);
    }
}


// 预览文件
function previewUrl($url){
    $previewConf=config('preview');
    $preview='';
    $suffix=explode('.',$url);
    $ext=$suffix[count($suffix)-1];
    $media=['jpg','jpeg','png','bmp','gif','pdf','mp3','wav','wmv','amr','mp4','3gp','avi','m2v','mkv','mov','webp'];
    $doc=['ppt','pptx','doc','docx','xls','xlsx','pdf'];
    if(in_array($ext,$media) && $previewConf['own']){
        $preview=$previewConf['own']."view.html?src=".$url;
    }elseif(in_array($ext,$doc) && $previewConf['yzdcs']){
        $preview=$previewConf['yzdcs'].'?k='.$previewConf['keycode'].'&url='.$url;
    }else{
        $preview=request()->domain()."view.html?src=".$url;
    }
    return $preview;
}

/**
 * 解析sql语句
 * @param  string $content sql内容
 * @param  int $limit  如果为1，则只返回一条sql语句，默认返回所有
 * @param  array $prefix 替换表前缀
 * @return array|string 除去注释之后的sql语句数组或一条语句
 */
function parse_sql($sql = '', $limit = 0, $prefix = []) {
    // 被替换的前缀
    $from = '';
    // 要替换的前缀
    $to = '';
    // 替换表前缀
    if (!empty($prefix)) {
        $to   = current($prefix);
        $from = current(array_flip($prefix));
    }
    if ($sql != '') {
        // 纯sql内容
        $pure_sql = [];
        // 多行注释标记
        $comment = false;
        // 按行分割，兼容多个平台
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        $sql = explode("\n", trim($sql));
        // 循环处理每一行
        foreach ($sql as $key => $line) {
            // 跳过空行
            if ($line == '') {
                continue;
            }
            // 跳过以#或者--开头的单行注释
            if (preg_match("/^(#|--)/", $line)) {
                continue;
            }
            // 跳过以/**/包裹起来的单行注释
            if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                continue;
            }
            // 多行注释开始
            if (substr($line, 0, 2) == '/*') {
                $comment = true;
                continue;
            }
            // 多行注释结束
            if (substr($line, -2) == '*/') {
                $comment = false;
                continue;
            }
            // 多行注释没有结束，继续跳过
            if ($comment) {
                continue;
            }
            // 替换表前缀
            if ($from != '') {
                $line = str_replace('`'.$from, '`'.$to, $line);
            }
            if ($line == 'BEGIN;' || $line =='COMMIT;') {
                continue;
            }
            // sql语句
            array_push($pure_sql, $line);
        }
        // 只返回一条语句
        if ($limit == 1) {
            return implode($pure_sql, "");
        }
        // 以数组形式返回sql语句
        $pure_sql = implode($pure_sql, "\n");
        $pure_sql = explode(";\n", $pure_sql);
        return $pure_sql;
    } else {
        return $limit == 1 ? '' : [];
    }
}

/**
 * 更新或添加环境变量
 *
 * @param string $key 环境变量的键
 * @param string $value 环境变量的值
 * @return bool 成功返回 true，失败返回 false
 */
function updateEnv($key, $value)
{
    $envFile = app()->getRootPath() . '.env';
    if (!file_exists($envFile) || !is_writable($envFile)){
        return false;
    }

    // 读取 .env 文件内容
    $envContent = file_get_contents($envFile);
    $keyPattern = preg_quote($key, '/');
    $pattern = "/^{$keyPattern}=(.*)\$/m";

    if (preg_match($pattern, $envContent)) {
        // 如果找到了键值对，替换其值
        $replacement = "{$key}={$value}";
        $newEnvContent = preg_replace($pattern, $replacement, $envContent);
    } else {
        // 如果没有找到键值对，添加新的键值对
        $newEnvContent = $envContent . PHP_EOL . "{$key}={$value}";
    }
    // 保存更新后的 .env 文件内容
    return file_put_contents($envFile, $newEnvContent) !== false;
}

// 获取文件的域名
function getDiskUrl(){
    $disk=env('filesystem.driver','local');
    $url=request()->domain();
    if($disk=='aliyun'){
        $url=env('filesystem.aliyun_url','');
    }elseif($disk=='qiniu'){
        $url=env('filesystem.qiniu_url','');
    }elseif($disk=='qcloud'){
        $url=env('filesystem.qcloud_cdn','');
    }
    $url=rtrim($url,'/');
    return $url;
}

/**
 * 合成图片
 * @param  array   $pic_list  [图片列表数组]
 * @param  boolean $is_save   [是否保存，true保存，false输出到浏览器]
 * @param  string  $save_path [保存路径]
 * @return boolean|string
 */
function getGroupAvatar($pic_list=array(),$is_save=false,$save_path=''){
    //验证参数
    if(empty($pic_list) || empty($save_path)){
        return false;
    }
    if($is_save){
        //如果需要保存，需要传保存地址
        if(empty($save_path)){
            return false;
        }
    }
    // 只操作前9个图片
    $pic_list = array_slice($pic_list, 0, 9);
    //设置背景图片宽高
    $bg_w = 150; // 背景图片宽度
    $bg_h = 150; // 背景图片高度
    //新建一个真彩色图像作为背景
    $background = imagecreatetruecolor($bg_w,$bg_h);
    //为真彩色画布创建白灰色背景，再设置为透明
    $color = imagecolorallocate($background, 202, 201, 201);
    imagefill($background, 0, 0, $color);
    imageColorTransparent($background, $color);
    //根据图片个数设置图片位置
    $pic_count = count($pic_list);
    $lineArr = array();//需要换行的位置
    $space_x = 3;
    $space_y = 3;
    $line_x = 0;
    switch($pic_count) {
        case 1: // 正中间
            $start_x = intval($bg_w/4); // 开始位置X
            $start_y = intval($bg_h/4); // 开始位置Y
            $pic_w = intval($bg_w/2); // 宽度
            $pic_h = intval($bg_h/2); // 高度
            break;
        case 2: // 中间位置并排
            $start_x = 2;
            $start_y = intval($bg_h/4) + 3;
            $pic_w = intval($bg_w/2) - 5;
            $pic_h = intval($bg_h/2) - 5;
            $space_x = 5;
            break;
        case 3:
            $start_x = 40; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/2) - 5; // 宽度
            $pic_h = intval($bg_h/2) - 5; // 高度
            $lineArr = array(2);
            $line_x = 4;
            break;
        case 4:
            $start_x = 4; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/2) - 5; // 宽度
            $pic_h = intval($bg_h/2) - 5; // 高度
            $lineArr = array(3);
            $line_x = 4;
            break;
        case 5:
            $start_x = 30; // 开始位置X
            $start_y = 30; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(3);
            $line_x = 5;
            break;
        case 6:
            $start_x = 5; // 开始位置X
            $start_y = 30; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(4);
            $line_x = 5;
            break;
        case 7:
            $start_x = 53; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(2,5);
            $line_x = 5;
            break;
        case 8:
            $start_x = 30; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(3,6);
            $line_x = 5;
            break;
        case 9:
            $start_x = 5; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(4,7);
            $line_x = 5;
            break;
    }
    foreach( $pic_list as $k=>$pic_path ) {
        $kk = $k + 1;
        if ( in_array($kk, $lineArr) ) {
            $start_x = $line_x;
            $start_y = $start_y + $pic_h + $space_y;
        }
        //获取图片文件扩展类型和mime类型，判断是否是正常图片文件
        //非正常图片文件，相应位置空着，跳过处理
        $image_mime_info = @getimagesize($pic_path);
        if($image_mime_info && !empty($image_mime_info['mime'])){
            $mime_arr = explode('/',$image_mime_info['mime']);
            if(is_array($mime_arr) && $mime_arr[0] == 'image' && !empty($mime_arr[1])){
                switch($mime_arr[1]) {
                    case 'jpg':
                    case 'jpeg':
                        $imagecreatefromjpeg = 'imagecreatefromjpeg';
                        break;
                    case 'png':
                        $imagecreatefromjpeg = 'imagecreatefrompng';
                        break;
                    case 'gif':
                    default:
                        $imagecreatefromjpeg = 'imagecreatefromstring';
                        $pic_path = file_get_contents($pic_path);
                        break;
                }
                //创建一个新图像
                $resource = $imagecreatefromjpeg($pic_path);
                //将图像中的一块矩形区域拷贝到另一个背景图像中
                // $start_x,$start_y 放置在背景中的起始位置
                // 0,0 裁剪的源头像的起点位置
                // $pic_w,$pic_h copy后的高度和宽度
                imagecopyresized($background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource));
            }
        }
        // 最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度
        $start_x = $start_x + $pic_w + $space_x;
    }
    if($is_save){
        $dir = pathinfo($save_path,PATHINFO_DIRNAME);
        if(!is_dir($dir)){
            $file_create_res = mkdir($dir,0777,true);
            if(!$file_create_res){
                return false;//没有创建成功
            }
        }
        $res = imagejpeg($background,$save_path);
        imagedestroy($background);
        if($res){
            return true;
        }else{
            return false;
        }
    }else{
        //直接输出
        header("Content-type: image/jpg");
        imagejpeg($background);
        imagedestroy($background);
    }
}

/**
 * 获取一个唯一token
 * @return string
 */
function getOnlyToken()
{
    return md5(uniqid(md5(microtime(true)), true));
}

// 设置排序规则
function orderBy($field, $type, $prefix = '', $default = 'update_time')
{
    $type=is_numeric($type)?($type==1?'asc':'desc'):$type;
    if ($field) {
        $order = $prefix . $field . ' ' . $type;
    } else {
        $order = $prefix . $default . ' desc';
    }
    return $order;
}

// 获取文件后缀图片
function getExtUrl($path){
    $ext=explode('.',$path);
    $ext=end($ext);
    // 如果是图片文件，就直接返回图片地址
    $image=['jpg','jpeg','png','bmp','gif','webp'];
    if(in_array($ext,$image)){
        return getFileUrl($path);
    }
    $extUrl='/static/img/ext/'.strtoupper($ext).'.png';
    // 判断文件是否存在
    if(!file_exists(public_path().$extUrl)){
        $extUrl='/static/img/ext/folder.png';
    }
    return request()->domain().$extUrl;
}