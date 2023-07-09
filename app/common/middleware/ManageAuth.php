<?php
namespace app\common\middleware;
//验证权限
class ManageAuth
{
    public function handle($request, \Closure $next)
    {
        
        // 设置演示模式,演示模式下无法修改配置
        $request->demonMode=env('app.demon_mode',false);
        if(!$request->demonMode){
            if($request->userInfo['user_id']!=1 || $request->userInfo['role']!=2){
                shutdown('您没有权限访问该接口',-1);
            }
        }else{
            $rules=[
                'User/add',
                'User/edit',
                'User/del',
                'User/setRole',
                'User/setsatus',
                'User/editPassword',
                'Group/del',
                'Group/changeOwner',
                'Group/delGroupUser',
                'Task/startTask',
                'Task/stopTask',
                'Config/setConfig'
            ];
            // 获取pathinfo信息
            $pathinfo = $request->pathinfo();
            if(in_array($pathinfo,$rules)){
                 return shutdown('演示模式下无法操作！',400);
            }
        }
        
        return $next($request);
    }
}