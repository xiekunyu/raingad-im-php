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
        if(Cache::has($name) && !$update){
            $systemInfo=Cache::get($name);
        }else{
            $systemInfo=[];
            $conf=Config::where([['name','in',['sysInfo','chatInfo']]])->select()->toArray();
            foreach($conf as $v){
                $systemInfo[$v['name']]=$v['value'];
            }
            Cache::set($name,$systemInfo,7*86400);
        }
        return $systemInfo;
    }
}