<?php

namespace app\enterprise\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Db;
use app\enterprise\model\{User, Message, GroupUser, Friend};
use GatewayClient\Gateway;
use Exception;
use think\facade\Cache;

class Im extends BaseController
{
    protected $fileType = ['file', 'image','video','voice'];
    // 获取联系人列表
    public function getContacts()
    {
        $data = User::getUserList([['status', '=', 1], ['user_id', '<>', $this->userInfo['user_id']]], $this->userInfo['user_id']);
        $count=Friend::where(['status'=>2,'friend_user_id'=>$this->uid])->count();
        $time=Friend::where(['status'=>2,'friend_user_id'=>$this->uid])->order('create_time desc')->value('create_time');
        return success('', $data,$count,strtotime($time)*1000);
    }

    protected function checkAuth($is_group,$toContactId=0)
    {
        $chatSetting=$this->chatSetting;
        if($is_group==0 && $chatSetting['simpleChat']==0){
            return shutdown('目前禁止用户私聊！',400);
        }
    }


    //发送消息
    public function sendMessage()
    {
        $param = $this->request->param();
        $param['user_id'] = $this->userInfo['user_id'];
        $is_group=$param['is_group']??0;
        $chatSetting=$this->chatSetting;
        if($is_group==0 && $chatSetting['simpleChat']==0){
            return warning('目前禁止用户私聊！');
        }
        // 如果是单聊，并且是社区模式，需要判断是否是好友
        if($is_group==0 && $this->globalConfig['sysInfo']['runMode']==2){
            $friend=Friend::where(['friend_user_id'=>$this->uid,'create_user'=>$param['toContactId']])->find();
            if(!$friend){
                return warning('您你在TA的好友列表，不能发消息！');
            }
            $otherFriend=Friend::where(['friend_user_id'=>$param['toContactId'],'create_user'=>$this->uid])->find();
            if(!$otherFriend){
                return warning('TA还不是您的好友，不能发消息！');
            }
        }
        $data = Message::sendMessage($param);
        if ($data) {
            return success('', $data);
        } else {
            return error('发送失败');
        }
    }

    // 获取用户信息
    public function getUserInfo()
    {
        $user_id = $this->request->param('user_id');
        $user=User::find($user_id);
        if(!$user){
            return error('用户不存在');
        }
        $user->avatar=avatarUrl($user->avatar,$user->realname,$user->user_id,120);
        // 查询好友关系
        $friend=Friend::where(['friend_user_id'=>$user_id,'create_user'=>$this->userInfo['user_id']])->find();
        $user->friend=$friend ? : '';
        $location='';
        if($user->last_login_ip){
            $location=implode(" ", \Ip::find($user->last_login_ip));
        }
        $user->location=$location;
        $user->password='';
        return success('', $user);
    }

    // 搜索用户
    public function searchUser(){
        $keywords=$this->request->param('keywords','');
        if(!$keywords){
            return success('',[]);
        }
        $map=['status'=>1,'account'=>$keywords];
        $list=User::where($map)->select()->toArray();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['avatar']=avatarUrl($v['avatar'],$v['realname'],$v['user_id'],120);
            }
        }
        return success('', $list);
    }

    // 获取聊天记录
    public function getMessageList()
    {
        $param = $this->request->param();
        $is_group = isset($param['is_group']) ? $param['is_group'] : 0;
        // 设置当前聊天消息为已读
        $chat_identify = $this->setIsRead($is_group, $param['toContactId']);
        $type = isset($param['type']) ? $param['type'] : '';
        $map = ['chat_identify' => $chat_identify, 'status' => 1, 'is_group' => $is_group];
        $where = [];
        if ($type && $type != "all") {
            $map['type'] = $type;
        } else {
            if (isset($param['type'])) {
                $where[] = ['type', '<>', 'event'];
            }
        }
        $keywords = isset($param['keywords']) ? $param['keywords'] : '';
        if ($keywords && in_array($type, ['text', 'all'])) {
            $where[] = ['content', 'like', '%' . $keywords . '%'];
        }
        $listRows = input('limit') ?: 20;
        $pageSize = input('page');
        $list = Message::getList($map, $where, 'msg_id desc', $listRows, $pageSize);
        $data = $this->recombileMsg($list);
        // 如果是消息管理器则不用倒序
        if (!isset($param['type'])) {
            $data = array_reverse($data);
        }
        return success('', $data, $list->total());
    }

    protected function recombileMsg($list)
    {
        $data = [];
        $userInfo = $this->userInfo;
        if ($list) {
            $listData = $list->toArray()['data'];
            $userList = User::matchUser($listData, true, 'from_user', 120);
            foreach ($listData as $k => $v) {
                // 屏蔽已删除的消息
                if ($v['del_user']) {
                    $delUser = explode(',', $v['del_user']);
                    if (in_array($userInfo['user_id'], $delUser)) {
                        unset($list[$k]);
                        continue;
                        // $v['type']="event";
                        // $v['content']="删除了一条消息";
                    }
                }
                $content = $v['content'];
                $preview = '';
                if (in_array($v['type'], $this->fileType)) {
                    $content = getFileUrl($v['content']);
                    $preview = previewUrl($content);
                }
                $fromUser = $userList[$v['from_user']];
                // 处理撤回的消息
                if ($v['type'] == "event") {
                    if ($v['from_user'] == $userInfo['user_id']) {
                        $content = "你" . $v['content'];
                    } elseif ($v['is_group'] == 1) {
                        $content = $fromUser['realname'] . $v['content'];
                    } else {
                        $content = "对方" . $v['content'];
                    }
                }
                $data[] = [
                    'msg_id' => $v['msg_id'],
                    'id' => $v['id'],
                    'status' => "successd",
                    'type' => $v['type'],
                    'sendTime' => $v['create_time'] * 1000,
                    'content' => $content,
                    'preview' => $preview,
                    'is_read' => $v['is_read'],
                    'is_group' => $v['is_group'],
                    'toContactId' => $v['to_user'],
                    'from_user' => $v['from_user'],
                    'fileName' => $v['file_name'],
                    'fileSize' => $v['file_size'],
                    'fromUser' => $fromUser,
                    'extends'=>is_string($v['extends'])?json_decode($v['extends'],true) : $v['extends']
                ];
            }
        }
        return $data;
    }

    // 设置当前窗口的消息默认为已读
    public function setMsgIsRead()
    {
        $param = $this->request->param();
        $this->setIsRead($param['is_group'], $param['toContactId']);
        if (!$param['is_group']) {
            wsSendMsg($param['fromUser'], 'isRead', $param['messages'], 0);
        }
        return success('');
    }

    // 设置消息已读
    protected function setIsRead($is_group, $to_user)
    {
        if ($is_group) {
            $chat_identify = $to_user;
            $toContactId = explode('-', $to_user)[1];
            // 更新群里面我的所有未读消息为0
            GroupUser::editGroupUser(['user_id' => $this->userInfo['user_id'], 'group_id' => $toContactId], ['unread' => 0]);
        } else {
            $chat_identify = chat_identify($this->userInfo['user_id'], $to_user);
            // 更新我的未读消息为0
            Message::update(['is_read' => 1], [['chat_identify', '=', $chat_identify], ['to_user', '=', $this->userInfo['user_id']]]);
        }
        return $chat_identify;
    }

    // 聊天设置
    public function setting()
    {
        $param = $this->request->param();
        if ($param) {
            User::where(['user_id' => $this->userInfo['user_id']])->update(['setting' => $param]);
            return success('');
        }
        return warning('设置失败');
    }

    // 撤回消息
    public function undoMessage()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $message = Message::where(['id' => $id])->find();
        if ($message) {
            $text = "撤回了一条消息";
            $fromUserName = "对方";
            $toContactId = $message['to_user'];
            if ($message['is_group'] == 1) {
                $fromUserName = $this->userInfo['realname'];
                $toContactId = $message['chat_identify'];
            }
            $message->content = $text;
            $message->type = 'event';
            $message->is_undo = 1;
            $message->create_time = time();
            $message->save();
            $data = $message->toArray();
            $data['content'] = $fromUserName . $text;
            wsSendMsg($toContactId, 'undoMessage', $data, $data['is_group']);
            return success('');
        } else {
            return warning();
        }
    }

    // 删除消息
    public function removeMessage()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $map = ['id' => $id];
        $message = Message::where($map)->find();
        if ($message) {
            $message->del_user = $this->userInfo['user_id'];
            if ($message['is_group'] == 1) {
                if ($message['del_user']) {
                    $message->del_user .= ',' . $this->userInfo['user_id'];
                }
            } else {
                if ($message['del_user'] > 0) {
                    $message->where($map)->delete();
                    return success('删除成功！');
                }
            }
            $message->save();
            return success('');
        } else {
            return warning('');
        }
    }

    // 消息免打扰
    public function isNotice()
    {
        $param = $this->request->param();
        $user_id = $this->userInfo['user_id'];
        $id = $param['id'];
        if ($param['is_group'] == 1) {
            $group_id = explode('-', $param['id'])[1];
            GroupUser::update(['is_notice' => $param['is_notice']], ['user_id' => $user_id, 'group_id' => $group_id]);
        } else {
            $map = ['create_user' => $user_id, 'friend_user_id' => $id];
            $friend = Friend::where($map)->find();
            try {
                if ($friend) {
                    $friend->is_notice = $param['is_notice'];
                    $friend->save();
                } else {
                    $info = [
                        'create_user' => $user_id,
                        'friend_user_id' => $id,
                        'is_notice' => $param['is_notice']
                    ];
                    Friend::create($info);
                }
                return success('');
            } catch (Exception $e) {
                return error($e->getMessage());
            }
        }
        wsSendMsg($user_id,"setIsNotice",['id'=>$id,'is_notice'=>$param['is_notice'],'is_group'=>$param['is_group']]);
        return success('');
    }

    // 设置聊天置顶
    public function setChatTop()
    {
        $param = $this->request->param();
        $user_id = $this->userInfo['user_id'];
        $is_group = $param['is_group'] ?: 0;
        $id = $param['id'];
        
        try {
            if ($is_group == 1) {
                $group_id = explode('-', $param['id'])[1];
                GroupUser::update(['is_top' => $param['is_top']], ['user_id' => $user_id, 'group_id' => $group_id]);
            } else {
                $map = ['create_user' => $user_id, 'friend_user_id' => $id];
                $friend = Friend::where($map)->find();
                if ($friend) {
                    $friend->is_top = $param['is_top'];
                    $friend->save();
                } else {
                    $info = [
                        'create_user' => $user_id,
                        'friend_user_id' => $id,
                        'is_top' => $param['is_top']
                    ];
                    Friend::create($info);
                }
            }
            wsSendMsg($user_id,"setChatTop",['id'=>$id,'is_top'=>$param['is_top'],'is_group'=>$is_group]);
            return success('');
        } catch (Exception $e) {
            return error($e->getMessage());
        }
    }
    
    // 删除聊天
    public function delChat()
    {
        $param = $this->request->param();
        $user_id = $this->userInfo['user_id'];
        $is_group = $param['is_group'] ?: 0;
        $id = $param['id'];
        if(!$is_group){
            $chat_identify=chat_identify($user_id,$id);
        }else{
            return success('');
        }
        Message::where(['chat_identify' => $chat_identify])->update(['is_last' => 0]);
        return success('');
    }

    // 向用户发送消息
    public function sendToMsg(){
        $param=$this->request->param();
        $toContactId=$param['toContactId'];
        
        $type=$param['type'];
        $status=$param['status'];
        $event=$param['event'] ?? 'calling';
        $sdp=$param['sdp'] ?? '';
        $iceCandidate=$param['iceCandidate'] ?? '';
        $callTime=$param['callTime'] ?? '';
        $msg_id=$param['msg_id'] ?? '';
        $id=$param['id'] ?? '';
        $code=$param['code'] ?? 901;
        // 如果该用户不在线，则发送忙线
        if(!Gateway::isUidOnline($toContactId)){
            $toContactId=$this->userInfo['user_id'];
            $code=907;
            $event='busy';
            sleep(1);
        }
        switch($code){
            case 901:
                $content='发起通话请求';
                break;
            case 902:
                $content='取消通话请求';
                break;
            case 903:
                $content='拒绝通话请求';
                break;
            case 904:
                $content='接听通话请求';
                break;
            case 905:
                $content='未接通';
                break;
            case 906:
                $content='通话时长'.$callTime.'秒';
                break;
            case 907:
                $content='忙线中';
                break;
            default:
                $content='数据交换中';
                break;
        }
        
        $data=[
            'id'=>uniqid(),
            'msg_id'=>$msg_id,
            'sendTime'=>time()*1000,
            'toContactId'=>$toContactId,
            'content'=>$content,
            'type'=>'webrtc',
            'status'=>'successd',
            'fromUser'=>$this->userInfo,
            'extends'=>[
                'type'=>$type,    //通话类型，1视频，0语音。
                'status'=>$status, //，1拨打方，2接听方
                'event'=>$event,
                'callTime'=>$callTime,
                'sdp'=>$sdp,
                'code'=>$code,  //通话状态:呼叫901，取消902，拒绝903，接听904，未接通905，接通后挂断906，忙线907
                'iceCandidate'=>$iceCandidate,
            ]
        ];
        wsSendMsg($toContactId,'webrtc',$data);
        return success('');
    }

    // 修改密码
    public function editPassword()
    {
        if(env('app.demon_mode',false)){
            return warning('演示模式不支持修改');
        }
        $code=$this->request->param('code','');
        $user_id = $this->userInfo['user_id'];
        $user=User::find($user_id);
        if(!$user){
            return warning('用户不存在');
        }
        $account=$user->account;
        if(Cache::get($account)!=$code){
            return warning('验证码不正确！');
        }
        try{
            $password = $this->request->param('password','');
            if($password){
                $salt=$user->salt;
                $user->password= password_hash_tp($password,$salt);
            }
            $user->save();
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

    // 修改用户信息
    public function updateUserInfo(){
        try{
            $data = $this->request->param();
            $user=User::find($this->uid);
            if(!$user){
                return warning('用户不存在');
            }
            $user->realname =$data['realname'];
            $user->email =$data['email'];
            $user->motto=$data['motto'];
            $user->sex =$data['sex'];
            $user->name_py= pinyin_sentence($data['realname']);
            $user->save();
            return success('修改成功', $data);
        }catch (\Exception $e){
            return error($e->getMessage());
        }
    }

    // 修改账户
    public function editAccount(){
        if(env('app.demon_mode',false)){
            return warning('演示模式不支持修改');
        }
        $code=$this->request->param('code','');
        $newCode=$this->request->param('newCode','');
        $account=$this->request->param('account','');
        $isUser=User::where('account',$account)->find();
        if($isUser){
            return warning('账户已存在');
        }
        $user=User::find($this->uid);
        if(!$user){
            return warning('用户不存在');
        }
        if(Cache::get($user->account)!=$code){
            return warning('验证码不正确！');
        }
        if(Cache::get($account)!=$newCode){
            return warning('新账户验证码不正确！');
        }
        try{
            $user->account=$account;
            $user->save();
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }
}
