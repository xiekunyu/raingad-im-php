<?php
namespace app\common\middleware;
//验证权限
class ManageAuth
{
    public function handle($request, \Closure $next)
    {
        if($request->userInfo['user_id']!=1){
            shutdown('您没有权限访问该接口',-1);
        }
        // 设置演示模式,演示模式下无法修改配置
        $request->demonMode=env('app.demon_mode',false);
        return $next($request);
    }
}