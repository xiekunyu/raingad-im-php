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

    // 发布公告
    public function publishNotice(){
        $userInfo=$this->userInfo['userInfo'];
        if($userInfo['user_id']!=1){
            return warning('system.noAuth');
        }
        $param=$this->request->param();
        $content="<h3>".$param['title']."</h3><br><p>".$param['content']."</p>";
        $data=[
            'from_user'=>$userInfo['user_id'],
            'to_user'=>0,
            'id'=>\utils\Str::getUuid(),
            'content'=>str_encipher($content,true),
            'chat_identify'=>'admin_notice',
            'create_time'=>time(),
            'type'=>'text',
            'is_group'=>2,
            'is_read'=>1,
            'is_top'=>0,
            'is_notice'=>1,
            'at'=>null,
            'pid'=>0,
            'extends'=>['title'=>$param['title']],
        ];
        Message::create($data);
        $msgInfo=$data;
        $msgInfo['status']='successd';
        $msgInfo['user_id']=$userInfo['user_id'];
        $msgInfo['sendTime']=time()*1000;
        $msgInfo['toContactId']='admin_notice';
        $msgInfo['fromUser']=[
            'id'=>$userInfo['user_id'],
            'avatar'=>avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id'],120),
            'displayName'=>$userInfo['realname']
        ];
        wsSendMsg(0,'simple',$msgInfo,1);
        return success('');
    }
}