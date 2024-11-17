<?php
namespace app\common\middleware;
//验证权限
class ManageAuth
{
    public function handle($request, \Closure $next)
    {
        
        // 设置演示模式,演示模式下无法修改配置
        $request->demonMode=env('app.demon_mode',false);
        if($request->userInfo['user_id']!=1){
            if(!$request->demonMode){
                if($request->userInfo['role']==0){
                    shutdown(lang('system.notAuth'),-1);
                }
            }else{
                $rules=[
                    'user/add',
                    'user/edit',
                    'user/del',
                    'user/setrole',
                    'user/setstatus',
                    'user/editpassword',
                    'group/del',
                    'group/changeowner',
                    'group/delgroupuser',
                    'task/starttask',
                    'task/stoptask',
                    'config/setconfig',
                    'index/publishnotice',
                    'index/delnotice',
                    'message/dealmsg',
                ];
                // 获取pathinfo信息
                $pathinfo = strtolower($request->pathinfo());
                if(in_array($pathinfo,$rules)){
                    return shutdown(lang('system.demoMode'),400);
                }
            }
        }
        return $next($request);
    }
}