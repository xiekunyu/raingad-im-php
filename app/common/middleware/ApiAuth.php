<?php
namespace app\common\middleware;

//验证权限
class ApiAuth
{
    public function handle($request, \Closure $next)
    {
        $apiStatus=config('app.api_status');
        if(!$apiStatus){
            return shutdown(lang('system.apiClose'));
        }
        $appId=config('app.app_id');
        $appSecret=config('app.app_secret');
        $header = $request->header();
        $app_id=$header['x-im-appid'] ?? '';
        $timeStamp=$header['x-im-timestamp'] ?? 0;
        $sign=$header['x-im-sign'] ?? '';
        if(!$app_id || !$timeStamp || !$sign){
            return shutdown(lang('system.parameterError'));
        }
        // 时间戳不能大约60秒
        if(time()-$timeStamp>60){
            return shutdown(lang('system.longTime'));
        }
        if($appId!=$app_id){
            return shutdown(lang('system.appIdError'));
        }
        $signStr=md5($appId.$timeStamp.$appSecret);
        if($sign!=$signStr){
            return shutdown(lang('system.signError'));
        }
        return $next($request);
    }
}