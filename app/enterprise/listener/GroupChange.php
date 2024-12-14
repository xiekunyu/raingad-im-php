<?php
namespace app\enterprise\listener;

use app\enterprise\model\{Group,User,Message};
use app\manage\model\Config;
use GatewayClient\Gateway;

// 监听群聊变更事件
class GroupChange
{
    public function handle(Group $group,User $user,$data){
        Gateway::$registerAddress = config('gateway.registerAddress');
        $groupInfo=$data['param'];
        // 如果是扫码进群，表示手动操作，手动操作需要触发客户端ID推送
        if($data['action'] == 'joinGroup'){
            Gateway::joinGroup(request()->header('clientId'),$data['group_id']);
        }elseif($data['action'] == 'autoCreateGroup'){
            // 自动创建的群通知群主，如果在线则推送
            if(Gateway::isUidOnline($groupInfo['owner_uid'])){
                wsSendMsg([$groupInfo['owner_uid']], 'addGroup', $groupInfo);
            }
        }elseif($data['action'] == 'editGroupName'){
            return;
        }
        $uid=$groupInfo['owner_uid'] ?? 1;
        $userInfo=$user->field('user_id,realname,avatar')->where(['user_id'=>$uid])->find();
        if($userInfo){
            $userInfo=$userInfo->toArray();
            $userInfo['id']=$userInfo['user_id'];
            $userInfo['avatar']=avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id']);
            // 发送入群事件
            $msg=[
                'id'=>\utils\Str::getUuid(),
                'user_id'=>$uid,
                'content'=>lang('group.join',['username'=>$groupInfo['joinerName'] ?? 'xxx ']),
                'toContactId'=>'group-'.$data['group_id'],
                'sendTime'=>time()*1000,
                'type'=>'event',
                'is_group'=>1,
                'status'=>'succeed',
                'fromUser'=>$userInfo,
                'at'=>[],
                'action'=>$data['action'],
            ];
            Message::sendMsg($msg,1);
        }
        return true;
    }
}