<?php

namespace app\enterprise\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Db;
use app\enterprise\model\{User, Message, GroupUser, Friend,Group};
use GatewayClient\Gateway;
use Exception;
use League\Flysystem\Util;
use think\facade\Cache;

class Im extends BaseController
{
    protected $fileType = ['file', 'image','video','voice','emoji'];
    // 获取联系人列表
    public function getContacts()
    {
        $data = User::getUserList([['status', '=', 1], ['user_id', '<>', $this->userInfo['user_id']]], $this->userInfo['user_id']);
        $count=Friend::where(['status'=>2,'friend_user_id'=>$this->uid])->count();
        $time=Friend::where(['friend_user_id'=>$this->uid,'is_invite'=>1])->order('create_time desc')->value('create_time');
        return success('', $data,$count,$time*1000);
    }

    // 获取单个人员的信息
    public function getContactInfo(){
        $id = $this->request->param('id');
        $is_group = is_string($id) ? 1 : 0;
        $user=new User;
        $data=$user->setContact($id,$is_group);
        if(!$data){
            return warning($user->getError());
        }
        return success('',$data);
    }


    //发送消息
    public function sendMessage()
    {
        $param = $this->request->param();
        $param['user_id'] = $this->userInfo['user_id'];
        $message=new Message();
        $data = $message->sendMessage($param,$this->globalConfig);
        if ($data) {
            return success('', $data);
        } else {
            return warning($message->getError());
        }
    }

    //转发消息
    public function forwardMessage()
    {
        $param = $this->request->param();
        $userIds=$param['user_ids'] ?? [];
        if(!$userIds || count($userIds)>5){
            return warning(lang('im.forwardLimit',['count'=>5]));
        }
        $msg_id=$param['msg_id'] ?? 0;
        $message=Message::find($msg_id);
        if(!$message){
            return warning(lang('im.exist'));
        }
        $message=$message->toArray();
        $userInfo=$this->userInfo;
        try{
            $is_group=0;
            $error=0;
            $chatSetting=$this->chatSetting;
            foreach($userIds as $k=>$v){
                $msgInfo=$message;
                if(strpos($v,'group')!==false){
                    $is_group=1;
                }else{
                    $is_group=0;
                }
                if($is_group==0 && $chatSetting['simpleChat']==0){
                    $error++;
                    continue;
                }
                $msgInfo['id']=\utils\Str::getUuid();
                $msgInfo['status']='successd';
                $msgInfo['user_id']=$userInfo['user_id'];
                $msgInfo['sendTime']=time()*1000;
                $msgInfo['toContactId']=$v;
                $msgInfo['content']=str_encipher($msgInfo['content'],false);
                $msgInfo['fromUser']=[
                    'id'=>$userInfo['user_id'],
                    'avatar'=>avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id'],120),
                    'displayName'=>$userInfo['realname']
                ];
                $msgInfo['is_group']=$is_group;
                // 如果是单聊，并且是社区模式，需要判断是否是好友
                if($is_group==0 && $this->globalConfig['sysInfo']['runMode']==2){
                    $friend=Friend::where(['friend_user_id'=>$this->uid,'create_user'=>$v])->find();
                    if(!$friend){
                        $error++;
                        continue;
                    }
                    $otherFriend=Friend::where(['friend_user_id'=>$v,'create_user'=>$this->uid])->find();
                    if(!$otherFriend){
                        $error++;
                        continue;
                    }
                }
                $message=new Message();
                $data=$message->sendMessage($msgInfo,$this->globalConfig);
                if(!$data){
                    return warning($message->getError());
                }
            }
        }catch(\Exception $e){
            return error($e->getMessage());
        }
        if ($error) {
            $text=lang('im.forwardRule',['count'=>$error]);
        } else {
            $text=lang('im.forwardOk');
        }
        return success($text);
    }

    // 获取用户信息
    public function getUserInfo()
    {
        $user_id = $this->request->param('user_id');
        $user=User::find($user_id);
        if(!$user){
            return error(lang('user.exist'));
        }
        $user->avatar=avatarUrl($user->avatar,$user->realname,$user->user_id,120);
        // 账号前面截取3位，后面截取两位，中间星号展示
        $user->account=substr($user->account, 0, 3).'******'.substr($user->account, -2, 2);
        // 查询好友关系
        $friend=Friend::where(['friend_user_id'=>$user_id,'create_user'=>$this->userInfo['user_id'],'status'=>1])->find();
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
        $list=User::where($map)->field(User::$defaultField)->where([['account','<>',$this->userInfo['account']]])->select()->toArray();
        if($list){
            $ids=array_column($list,'user_id');
            $friendList=Friend::getFriend([['create_user','=',$this->uid],['friend_user_id','in',$ids]]);
            foreach($list as $k=>$v){
                $list[$k]['avatar']=avatarUrl($v['avatar'],$v['realname'],$v['user_id'],120);
                $list[$k]['friend']=$friendList[$v['user_id']] ?? '';
            }
        }
        return success('', $list);
    }

    // 获取系统所有人员加搜索
    public function userList(){
        $keywords=$this->request->param('keywords','');
        $listRows=$this->request->param('limit',20);
        $page=$this->request->param('page',1);
        $map=['status'=>1];
        $field="user_id,realname,avatar";
        if(!$keywords){
            $list=User::where($map)->field($field)->order('user_id asc')->limit(20)->paginate(['list_rows'=>$listRows,'page'=>$page]);;
            if($list){
                $list=$list->toArray()['data'];
            }
        }else{
            $list=User::where($map)->field($field)->where([['account','<>',$this->userInfo['account']]])->whereLike('account|realname|name_py','%'.$keywords.'%')->select()->toArray();
        }
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['avatar']=avatarUrl($v['avatar'],$v['realname'],$v['user_id'],120);
                $list[$k]['id']=$v['user_id'];
            }
        }
        return success('', $list);
    }

    // 获取聊天记录
    public function getMessageList()
    {
        $param = $this->request->param();
        $is_group = isset($param['is_group']) ? $param['is_group'] : 0;
        // 如果toContactId是数字，绝对是单聊
        $is_group = is_numeric($param['toContactId']) ? 0 : $is_group;
        // 设置当前聊天消息为已读
        $chat_identify = $this->setIsRead($is_group, $param['toContactId']);
        $type = isset($param['type']) ? $param['type'] : '';
        $is_at = isset($param['is_at']) ? $param['is_at'] : '';
        $map = ['chat_identify' => $chat_identify, 'status' => 1];
        $where = [];
        if ($type && $type != "all") {
            $map['type'] = $type;
        } else {
            if (isset($param['type'])) {
                $where[] = ['type', '<>', 'event'];
            }
        }
        // 群聊查询入群时间以后的消息
        if($is_group==1){
            $group_id = explode('-', $param['toContactId'])[1];
            $group=Group::where(['group_id'=> $group_id])->find();
            if($group && $group['setting']){
                $groupSetting=json_decode($group['setting'],true);
                $history=$groupSetting['history'] ?? false;
                // 如果开启了历史记录才可以查看所有记录，否者根据进群时间查询记录
                if(!$history){
                    $createTime=GroupUser::where(['group_id'=> $group_id,'user_id'=>$this->userInfo['user_id']])->value('create_time');
                    $where[] = ['create_time', '>=', $createTime ? : 0];
                }
            }
        }
        $keywords = isset($param['keywords']) ? $param['keywords'] : '';
        if ($keywords && in_array($type, ['text', 'all'])) {
            $where[] = ['content', 'like', '%' . $keywords . '%'];
        }
        // 如果是查询@数据
        if($is_at){
            $atList=Db::name('message')->where($map)->where($where)->whereFindInSet('at',$this->userInfo['user_id'])->order('msg_id desc')->select()->toArray();
            if($atList){
                $data = $this->recombileMsg($atList,false);
                Message::setAtread($data,$this->userInfo['user_id']);
                return success('', $data, count($data));
            }else{
                return success('', [], 0);
            }
        }
        $listRows = $param['limit'] ?: 20;
        $pageSize = $param['page'] ?: 1;
        $last_id = $param['last_id'] ?? 0;
        if($last_id){
            $where[]=['msg_id','<',$last_id];
        }
        $list = Message::getList($map, $where, 'msg_id desc', $listRows, $pageSize);
        $data = $this->recombileMsg($list);
        // 如果是群聊并且是第一页消息，需要推送@数据给用户
        if($param['is_group']==1 && $param['page']==1){
            $isPush=Cache::get('atMsgPush'.$chat_identify) ?? '';
            $atList=Db::name('message')->where(['chat_identify'=>$chat_identify,'is_group'=>1])->whereFindInSet('at',$this->userInfo['user_id'])->order('msg_id desc')->select()->toArray();
            $msgIda=array_column($atList,'msg_id');
            // 如果两次推送at数据的列表不一样，则推送
            if($isPush!=json_encode($msgIda)){
                $atData=$this->recombileMsg($atList,false);
                wsSendMsg($this->userInfo['user_id'],'atMsgList',[
                    'list'=>$atData,
                    'count'=>count($atData),
                    'toContactId'=>$param['toContactId']
                ]);
                Cache::set('atMsgPush'.$chat_identify,json_encode($msgIda),60);
            }
        }
        // 如果是消息管理器则不用倒序
        if (!isset($param['type'])) {
            $data = array_reverse($data);
        }
        return success('', $data, $list->total());
    }

    protected function recombileMsg($list,$isPagination=true)
    {
        $data = [];
        $userInfo = $this->userInfo;
        if ($list) {
            $listData = $isPagination ? $list->toArray()['data'] : $list;
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
                $content = str_encipher($v['content'],false);
                $preview = '';
                $ext='';
                if (in_array($v['type'], $this->fileType)) {
                    $content = getFileUrl($content);
                    $preview = previewUrl($content);
                    $ext=getExtUrl($content);
                }
                
                $fromUser = $userList[$v['from_user']];
                // 处理撤回的消息
                if ($v['type'] == "event" && $v['is_undo']==1) {
                    if ($v['from_user'] == $userInfo['user_id']) {
                        $content = lang('im.you'). $content;
                    } elseif ($v['is_group'] == 1) {
                        $content = $fromUser['realname'] . $content;
                    } else {
                        $content = lang('im.other') . $content;
                    }
                }
                $toContactId=$v['is_group'] ==1 ?  'group-'.$v['to_user'] : $v['to_user'];
                $atList=($v['at'] ?? null) ? explode(',',$v['at']): [];
                $data[] = [
                    'msg_id' => $v['msg_id'],
                    'id' => $v['id'],
                    'status' => "succeed",
                    'type' => $v['type'],
                    'sendTime' => $v['create_time'] * 1000,
                    'content' => $content,
                    'preview' => $preview,
                    'download' => $v['file_id'] ? getMainHost().'/filedown/'.encryptIds($v['file_id']) : '',
                    'is_read' => $v['is_read'],
                    'is_group' => $v['is_group'],
                    'at' => $atList,
                    'toContactId' => $toContactId,
                    'from_user' => $v['from_user'],
                    'file_id' => $v['file_id'],
                    'file_cate' => $v['file_cate'],
                    'fileName' => $v['file_name'],
                    'fileSize' => $v['file_size'],
                    'fromUser' => $fromUser,
                    'extUrl'=>$ext,
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
        
        // 判断是否是一个二维数组
        if (is_array($param['messages'][0] ?? '')) {
           $messages=$param['messages'];
        } else {
            $messages=[$param['messages']];
        }
        $this->setIsRead($param['is_group'], $param['toContactId'],$messages);
        if (!$param['is_group']) {
            wsSendMsg($param['fromUser'], 'isRead', $messages, 0);
        }
        return success('');
    }

    // 设置消息已读
    protected function setIsRead($is_group, $to_user,$messages=[])
    {
        if ($is_group==1) {
            $chat_identify = $to_user;
            $toContactId = explode('-', $to_user)[1];
            // 将@消息放到定时任务中逐步清理
            if($messages){
                Message::setAtRead($messages,$this->userInfo['user_id']);
            }
            // 更新群里面我的所有未读消息为0
            GroupUser::editGroupUser(['user_id' => $this->userInfo['user_id'], 'group_id' => $toContactId], ['unread' => 0]);
        } else if($is_group==0) {
            $chat_identify = chat_identify($this->userInfo['user_id'], $to_user);
            // 更新我的未读消息为0
            Message::update(['is_read' => 1], [['chat_identify', '=', $chat_identify], ['to_user', '=', $this->userInfo['user_id']]]);
            // 告诉对方我阅读了消息
            wsSendMsg($to_user, 'readAll', ['toContactId' => $this->userInfo['user_id']]);
        } else if($is_group==2){
            $chat_identify = $to_user; 
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
        return warning('');
    }

    // 撤回消息
    public function undoMessage()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $message = Message::where(['id' => $id])->find();
        if ($message) {
            // 如果时间超过了2分钟也不能撤回
            $createTime=is_string($message['create_time']) ? strtotime($message['create_time']) : $message['create_time'];
            if(time()-$createTime>120 && $message['is_group']==0){
                return warning(lang('im.redoLimitTime',['limit'=>2]));
            }
            $text = lang('im.redo');
            $fromUserName = lang('im.other');
            $toContactId = $message['to_user'];
            if ($message['is_group'] == 1) {
                $fromUserName = $this->userInfo['realname'];
                $toContactId = explode('-', $message['chat_identify'])[1];
                // 如果是群聊消息撤回，需要判断是否是群主或者管理员，如果是则可以撤回
                if($message['from_user']!=$this->userInfo['user_id']){
                    $groupUser=GroupUser::where(['user_id'=>$this->userInfo['user_id'],'group_id'=>$toContactId])->find();
                    if(!$groupUser || !in_array($groupUser['role'],[1,2])){
                        return warning(lang('system.notAuth'));
                    }
                    $text=lang('im.manageRedo');
                }
            }
            $message->content = str_encipher($text);
            $message->type = 'event';
            $message->is_undo = 1;
            //@的数据清空
            $message->at = ''; 
            $message->save();
            $info = $message->toArray();
            // $data = $info;
            $data['content'] = $fromUserName . $text;
            $data['sendTime'] = $createTime * 1000;
            $data['id'] = $info['id'];
            $data['from_user'] = $info['from_user'];
            $data['msg_id'] = $info['msg_id'];
            $data['status'] = $info['status'];
            $data['type'] = 'event';
            $data['is_last'] = $info['is_last'];
            $data['toContactId'] = $message['is_group'] == 1 ? $info['chat_identify'] : $toContactId;
            $data['isMobile'] = $this->request->isMobile() ? 1 : 0;
            wsSendMsg($toContactId, 'undoMessage', $data, $info['is_group']); 
            if($info['is_group']==0){
               // 给自己也发一份推送，多端同步
                $data['content'] =lang('im.you'). $text;
                wsSendMsg($this->userInfo['user_id'], 'undoMessage', $data, $info['is_group']); 
            }
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
                    return success(lang('system.delOk'));
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
        if($event=='calling'){
            $status=3;
        }
        $sdp=$param['sdp'] ?? '';
        $iceCandidate=$param['iceCandidate'] ?? '';
        $callTime=$param['callTime'] ?? '';
        $msg_id=$param['msg_id'] ?? '';
        $id=$param['id'] ?? '';
        $code=($param['code'] ?? '') ?: 901;
        // 如果该用户不在线，则发送忙线
        Gateway::$registerAddress = config('gateway.registerAddress');
        if(!Gateway::isUidOnline($toContactId)){
            $toContactId=$this->userInfo['user_id'];
            $code=907;
            $event='busy';
            sleep(1);
        }
        switch($code){
            case 902:
                $content=lang('webRtc.cancel');
                break;
            case 903:
                $content=lang('webRtc.refuse');
                break;
            case 905:
                $content=lang('webRtc.notConnected');
                break;
            case 906:
                $content=lang('webRtc.duration',['time'=>date("i:s",$callTime)]);
                break;
            case 907:
                $content=lang('webRtc.busy');
                break;
            case 908:
                $content=lang('webRtc.other');
                break;
            default:
                $content=$type==1 ?lang('webRtc.video') : lang('webRtc.audio');
                break;
        }
        switch($event){
            case 'calling':
                $content=$type==1 ?lang('webRtc.video'): lang('webRtc.audio');
                break;
            case 'acceptRtc':
                $content=lang('webRtc.answer');
                break;
            case 'iceCandidate':
                $content=lang('webRtc.exchange');
                break;
        }
        $userInfo=$this->userInfo;
        $userInfo['id']=$userInfo['user_id'];
        $user = new User();
        $data=[
            'id'=>$id,
            'msg_id'=>$msg_id,
            'sendTime'=>time()*1000,
            'toContactId'=>$toContactId,
            'content'=>$content,
            'type'=>'webrtc',
            'status'=>'succeed',
            'is_group'=>0,
            'is_read'=>0,
            'fromUser'=>$userInfo,
            'at'=>[],
            'extends'=>[
                'type'=>$type,    //通话类型，1视频，0语音。
                'status'=>$status, //，1拨打方，2接听方
                'event'=>$event,
                'callTime'=>$callTime,
                'sdp'=>$sdp,
                'code'=>$code,  //通话状态:呼叫901，取消902，拒绝903，接听904，未接通905，接通后挂断906，忙线907,其他端操作908
                'iceCandidate'=>$iceCandidate,
                'isMobile'=>$this->request->isMobile() ? 1 : 0,
            ]
        ];
        if($event=='calling'){
            $chat_identify=chat_identify($userInfo['id'],$toContactId);
            $msg=[
                'from_user'=>$userInfo['id'],
                'to_user'=>$toContactId,
                'id'=>$id,
                'content'=>str_encipher($content),
                'chat_identify'=>$chat_identify,
                'create_time'=>time(),
                'type'=>$data['type'],
                'is_group'=>0,
                'is_read'=>0,
                'extends'=>$data['extends'],
            ];
            $message=new Message();
            $message->update(['is_last'=>0],['chat_identify'=>$chat_identify]);
            $message->save($msg);
            $msg_id=$message->msg_id;
            $data['msg_id']=$msg_id;
            // 将接收人设置为发送人才能定位到该消息
            $data['toContactId']=$userInfo['id'];
            $data['toUser']=$toContactId;
        }elseif($event=='hangup'){
            $message=Message::where(['id'=>$id])->find();
            if(!$message){
                return error(lang('webRtc.fail'));
            }
            if($message){
                $message->content=str_encipher($content);
                $extends=$message->extends;
                $extends['code']=$code;
                $extends['callTime']=$callTime;
                $message->extends=$extends;
                $message->save();
            }
        }
        wsSendMsg($toContactId,'webrtc',$data);
        $wsData=$data;
        if(in_array($event,['calling','acceptRtc','hangup'])){
            if(in_array($event,['acceptRtc','hangup'])){
                $data['extends']['event']='otherOpt'; //其他端操作
            }
            $data['toContactId']=$toContactId;
            $data['contactInfo']=$user->setContact($toContactId,0,'webrtc',$content) ? : [];
            wsSendMsg($userInfo['id'],'webrtc',$data);
        }
        return success('',$wsData);
    }

    // 修改密码
    public function editPassword()
    {
        if(env('app.demon_mode',false)){
            return warning(lang('system.demoMode'));
        }
        
        $user_id = $this->userInfo['user_id'];
        $user=User::find($user_id);
        if(!$user){
            return warning(lang('user.exist'));
        }
        $account=$user->account;
        $code=$this->request->param('code','');
        $originalPassword = $this->request->param('originalPassword', '');
        if($code){
            if(Cache::get($account)!=$code){
                return warning(lang('user.codeErr'));
            }
        }elseif($originalPassword){
            if(password_hash_tp($originalPassword,$user->salt)!= $user->password){
                return warning(lang('user.passErr'));
            }
        }else{
            return warning(lang('system.parameterError'));
        }
        try{
            $password = $this->request->param('password','');
            if($password){
                $salt=$user->salt;
                $user->password= password_hash_tp($password,$salt);
            }
            $user->save();
            return success(lang('system.editOk'));
        }catch (\Exception $e){
            return error(lang('system.editFail'));
        }
    }

    // 修改用户信息
    public function updateUserInfo(){
        try{
            $data = $this->request->param();
            $user=User::find($this->uid);
            if(!$user){
                return warning(lang('user.exist'));
            }
            // 接入用户名检测服务
            event('GreenText',['content'=>$data['realname'],'service'=>"nickname_detection"]);
            // 个性签名检测服务
            event('GreenText',['content'=>$data['motto'],'service'=>"comment_detection"]);
            $user->realname =$data['realname'];
            $user->email =$data['email'];
            $user->motto=$data['motto'];
            $user->sex =$data['sex'];
            $user->name_py= pinyin_sentence($data['realname']);
            $user->save();
            return success(lang('system.editOk'), $data);
        }catch (\Exception $e){
            return error($e->getMessage());
        }
    }

    // 修改账户
    public function editAccount(){
        if(env('app.demon_mode',false)){
            return warning(lang('system.demoMode'));
        }
        $code=$this->request->param('code','');
        $newCode=$this->request->param('newCode','');
        $account=$this->request->param('account','');
        $isUser=User::where('account',$account)->find();
        if($isUser){
            return warning(lang('user.already'));
        }
        $user=User::find($this->uid);
        if(!$user){
            return warning(lang('user.exist'));
        }
        // 如果已经认证过了，则需要验证验证码
        if($user->is_auth){
            if(Cache::get($user->account)!=$code){
                return warning(lang('user.codeErr'));
            }
        }
        if(Cache::get($account)!=$newCode){
            return warning(lang('user.newCodeErr'));
        }
        try{
            $user->account=$account;
            $user->is_auth=1;
            $user->save();
            return success(lang('system.editOk'));
        }catch (\Exception $e){
            return error(lang('system.editFail'));
        }
    }

    // 阅读@消息
    public function readAtMsg(){
        $param = $this->request->param();
        $atList=Db::name('message')->where(['chat_identify'=>$param['toContactId'],'is_group'=>1])->whereFindInSet('at',$this->userInfo['user_id'])->order('msg_id desc')->select();
        $atData=$this->recombileMsg($atList,false);
        Message::setAtRead($atData,$this->userInfo['user_id']);
        // $message=Message::where('msg_id',$param['msg_id'])->select();
        // $atList=($message ?? null) ? explode(',',$message): [];
        // // 两个数组取差集
        // $newAtList = array_diff($atList, [$this->userInfo['user_id']]);
        // Message::where('msg_id',$param['msg_id'])->update(['at'=>implode(',',$newAtList)]);
        return success('');
    }

    // 获取系统公告
    public function getAdminNotice(){
        $data=Message::where(['chat_identify'=>'admin_notice'])->order('msg_id desc')->find();
        $extends=$data['extends'] ?? [];
        if(!$extends){
            $extends['title']='';
        }
        $createTime=$data['create_time'] ?? 0;
        if(!$createTime){
            $extends['create_time']=$createTime;
        }else{
            $extends['create_time']=is_string($data['create_time']) ? strtotime($data['create_time']) : $data['create_time'];
        }
       
        return success('',$extends);
    }

    // 双向删除消息
    public function delMessage(){
        $param = $this->request->param();
        $id = $param['id'];
        if(!$this->globalConfig['chatInfo']['dbDelMsg']){
            return warning(lang('system.noAuth'));
        }
        $message = Message::where(['id' => $id])->find();
        if ($message) {
            if($message['from_user']!=$this->userInfo['user_id']){
                return warning(lang('system.noAuth'));
            }
            Message::where(['id' => $id])->delete();
            // 如果是最后一条消息，需要将上一条设置为最后一条
            if($message['is_last']){
                Message::where(['chat_identify'=>$message['chat_identify']])->order('msg_id desc')->limit(1)->update(['is_last'=>1]);
            }
            $toContactId = $message['to_user'];
            if ($message['is_group'] == 1) {
                $toContactId = explode('-', $message['chat_identify'])[1];
            }
            wsSendMsg($toContactId, 'delMessage', $message, $message['is_group']); 
            return success('');
        } else {
            return warning(lang('im.exist'));
        }
    }
}
