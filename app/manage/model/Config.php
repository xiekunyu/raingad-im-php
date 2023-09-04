<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\manage\model;

use app\BaseModel;
use think\facade\Cache;
class Config extends BaseModel
{
    protected $json = ['value'];
    protected $jsonAssoc = true;

    // 获取系统配置信息
    public static function getSystemInfo($update=false){
        $name='systemInfo';
        // $auth=request()->header('Authorization');
        $nameFields=['sysInfo','fileUpload','chatInfo'];
        // 如果是登录状态才会返回chatINfo
        // if($auth){
        //     $name='all'.$name;
        //     $nameFields[]="chatInfo";
        // }
        if(Cache::has($name) && !$update){
            $systemInfo=Cache::get($name);
        }else{
            $systemInfo=[];
            $conf=Config::where([['name','in',$nameFields]])->select()->toArray();
            foreach($conf as $v){
                $value=[];
                if($v['name']=='fileUpload'){
                    $value['size'] = $v['value']['size'];
                    $value['preview'] = $v['value']['preview'];
                    $value['fileExt'] = $v['value']['fileExt'];
                }else{
                    $value=$v['value'];
                }
                $systemInfo[$v['name']]=$value;
            }
            Cache::set($name,$systemInfo,7*86400);
        }
        return $systemInfo;
    }
}