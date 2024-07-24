<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/14 17:24
 */


namespace app\manage\controller;


use app\BaseController;
use app\enterprise\model\{Message};
use think\facade\Cache;
class Index extends BaseController
{
    // 超级管理员专属功能

    // 清理消息
    public function clearMessage(){
        if($this->userInfo['user_id']!=1){
            return warning('system.noAuth');
        }
        Message::where(['status'=>1])->delete();
        return success('system.clearOk');
    }
}