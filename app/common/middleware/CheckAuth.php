<?php
namespace app\common\middleware;
use think\facade\Cache;
//验证权限
class CheckAuth
{
    public function handle($request, \Closure $next)
    {
    //    $userInfo=[
    //         'clientId'=>'',
    //         'user_id'=>1,
    //         'realname'=>'管理员',
    //         'account'=>'admin',
    //         'avatar'=>"http://im.raingad.com/avatar/%E7%AE%A1%E7%90%86%E5%90%9B/80/1"
    //     ];
    // 不验证登录接口
        // 验证用户是否有登陆信息
        $authToken=$request->header('authToken');
        $userInfo=[];
        if($authToken){
            $userInfo=Cache::get($authToken);
        }
        if(!$userInfo){
            shutdown('您的登陆已过期，请重新登陆',-1);
        }
        // 如果已经登陆，将用户信息存入请求头
        $request->userInfo=$userInfo;
        return $next($request);
    }
}