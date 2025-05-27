<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/14 17:24
 */


namespace app\manage\controller;


use app\BaseController;
use app\enterprise\model\{Message,User,Group,File};
use GatewayClient\Gateway;
class Index extends BaseController
{
    // 超级管理员专属功能
    public function index(){
        Gateway::$registerAddress = config('gateway.registerAddress');
        $client_id=$this->request->param('client_id','');
        $is_join=0;
        if($client_id){
            Gateway::joinGroup($client_id, 'admin-manage');
             $is_join=1;
        }
        $data=[
            'userCount'=>User::where(['status'=>1])->count(),
            'groupCount'=>Group::where(['status'=>1])->count(),
            'messageCount'=>Message::where(['status'=>1])->where([['type', 'not in', ['event','admin_notice','webrtc']]])->count(),
            'fileCount'=>File::where(['status'=>1])->count(),
            'onlineCount'=>Gateway::getAllUidCount() ?? 0,
            'clientCount'=>Gateway::getAllClientCount() ?? 0,
            'isJoin'=>$is_join,
        ];
        return success('', $data);
    }

    // 清理消息
    public function clearMessage(){
        if($this->userInfo['user_id']!=1){
            return warning('system.notAuth');
        }
        Message::where(['status'=>1])->delete();
        return success('system.clearOk');
    }

    // 公告列表
    public function noticeList(){
        $model=new Message();
        // 排序
        $order='msg_id DESC';
        $map=['chat_identify'=>"admin_notice"];
        $list = $this->paginate($model->where($map)->order($order));
        if ($list) {
            $data = $list->toArray()['data'];
            foreach($data as $k=>$v){
                $data[$k]['title']=$v['extends']['title'];
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }

    // 删除公告
    public function delNotice(){
        $param=$this->request->param();
        $msgId=$param['id'] ?:0;
        $map=['msg_id'=>$msgId];
        $message=Message::where($map)->find();
        if($message){
            Message::where($map)->delete();
        }
        return success('');
    }

    // 发布公告
    public function publishNotice(){
        $userInfo=$this->userInfo;
        if($userInfo['user_id']!=1){
            return warning('system.notAuth');
        }
        $param=$this->request->param();
        $msgId=$param['msgId'] ?? 0;
        $content="<h4>".$param['title']."</h4><br><p>".$param['content']."</p>";
        $data=[
            'from_user'=>$userInfo['user_id'],
            'to_user'=>0,
            'content'=>str_encipher($content,true),
            'chat_identify'=>'admin_notice',
            'create_time'=>time(),
            'type'=>'text',
            'is_group'=>2,
            'is_read'=>1,
            'is_top'=>0,
            'is_notice'=>1,
            'at'=>[],
            'pid'=>0,
            'extends'=>['title'=>$param['title'],'notice'=>$param['content']],
        ];
        if($msgId){
            Message::where(['msg_id'=>$msgId])->update([
                'content'=>$data['content'],
                'extends'=>$data['extends'],
            ]);
        }else{
            $data['id']=\utils\Str::getUuid();
            $message=new Message();
            $message->save($data);
            $msgId=$message->msg_id;
        }
        $msgInfo=$data;
        $msgInfo['status']='successd';
        $msgInfo['msg_id']=$msgId;
        $msgInfo['user_id']=$userInfo['user_id'];
        $msgInfo['sendTime']=time()*1000;
        $msgInfo['toContactId']='admin_notice';
        $msgInfo['to_user']='admin_notice';
        $msgInfo['content']=$param['title'];
        $msgInfo['fromUser']=[
            'id'=>$userInfo['user_id'],
            'avatar'=>avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id'],120),
            'displayName'=>$userInfo['realname']
        ];
        wsSendMsg(0,'simple',$msgInfo,1);
        return success('');
    }
}