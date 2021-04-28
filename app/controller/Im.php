<?php
namespace app\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Db;
use app\model\{User,Message,GroupUser};
class Im extends BaseController
{

    // 获取联系人列表
   public function getContacts(){
       $data=User::getUserList([['status','=',1],['user_id','<>',$this->userInfo['user_id']]],$this->userInfo['user_id']);
       return success('',$data);
   }

    //发送消息
    public function sendMessage(){
        $param=$this->request->param();
        $param['user_id']=$this->userInfo['user_id'];
        $data=Message::sendMessage($param);
        if($data){
            return success('',$data);
        }else{
            return error('发送失败');
        }
        
    }

    // 获取聊天记录
    public function getMessageList(){
        $is_group=$this->postData['is_group']?:0;
        // 设置当前聊天消息为已读
        $chat_identify=$this->setIsRead($is_group,$this->postData['toContactId']);
        $map=['chat_identify'=>$chat_identify,'status'=>1,'is_group'=>$is_group];
        $listRows =input('listRows')?:20;
        $pageSize=input('pageSize');
        $data=Message::getList($map,'','msg_id desc',$listRows,$pageSize);
        $data = array_reverse($data);
        return success('',$data);

    }

    // 设置当前窗口的消息默认为已读
    public function setMsgIsRead(){
        $param=$this->request->param();
        $this->setIsRead($param['is_group'],$param['toContactId']);
        if(!$param['is_group']){
            wsSendMsg($param['fromUser'],'isRead',$param['messages'],0);
        }
        return success();
    }

    // 设置消息已读
    protected function setIsRead($is_group,$to_user){
        if($is_group){
            $chat_identify=$to_user;
            $toContactId=explode('-',$to_user)[1];
            // 更新群里面我的所有未读消息为0
            GroupUser::editGroupUser(['user_id'=>$this->userInfo['user_id'],'group_id'=>$toContactId],['unread'=>0]);
        }else{
            $chat_identify=chat_identify($this->userInfo['user_id'],$to_user);
            // 更新我的未读消息为0
            Message::update(['is_read'=>1],[['chat_identify','=',$chat_identify],['to_user','=',$this->userInfo['user_id']]]);
        }
         return $chat_identify;
    }

    // 聊天设置
    public function setting(){
        $param=$this->request->param();
        if($param){
            User::where(['user_id'=>$this->userInfo['user_id']])->update(['setting'=>json_encode($param)]);
            return success();
        }
        return warning('设置失败');
    }

}
