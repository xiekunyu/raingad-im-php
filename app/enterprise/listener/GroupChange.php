<?php
namespace app\enterprise\listener;

use app\enterprise\model\{Group,User};
use GatewayClient\Gateway;

// 监听群聊变更事件
class GroupChange
{
    public function handle(Group $group,User $user,$data){
        Gateway::$registerAddress = config('gateway.registerAddress');
        if($data['action'] == 'joinGroup'){
            Gateway::joinGroup(request()->header('clientId'),$data['group_id']);
        }
    }
}