<?php
namespace app\common\listener;

use app\enterprise\model\{User,Message,Group,GroupUser};
use app\manage\model\{Config};

// 监听用户注册后的操作
class UserRegister
{

    public function handle(User $user,$data){
        try{
            // 查询相关配置信息
            $sysInfo=Config::where(['name'=>'sysInfo'])->value('value');
            if($sysInfo['runMode']!=2){
                return true;
            }
            // 获取聊天设置
            $chatInfo=Config::where(['name'=>'chatInfo'])->value('value');
            $autoAdduser=$chatInfo['autoAddUser'] ?? [];
            $autoTask=$this->createConf();
            // 是否开启自动客服
            if($autoAdduser && $autoAdduser['status']==1){
                // 获取客服ID
                if($autoTask['user_id']!=0){
                    $autoTask['user_id']=$this->findNextOrFirstId($autoAdduser['user_ids'], $autoTask['user_id'] ? : $autoAdduser['user_ids'][0]);
                }else{
                    $autoTask['user_id']=$autoAdduser['user_ids'][0] ?? '';
                }
                if($autoTask['user_id']){
                    $user->update(['cs_uid'=>$autoTask['user_id']],['user_id'=>$data['user_id']]);
                    // 如果设置了欢迎语则发送欢迎语
                    if($autoAdduser['welcome'] ?? ''){
                        $userInfo=$user->field('user_id,realname,avatar')->where(['user_id'=>$autoTask['user_id']])->find();
                        if($userInfo){
                            $userInfo['dispalayName']=$userInfo['realname'];
                            $userInfo['id']=$userInfo['user_id'];
                            $userInfo['avatar']=avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id']);
                            $msg=[
                                'id'=>\utils\Str::getUuid(),
                                'user_id'=>$autoTask['user_id'],
                                'content'=>$autoAdduser['welcome'],
                                'toContactId'=>$data['user_id'],
                                'sendTime'=>time()*1000,
                                'type'=>'text',
                                'is_group'=>0,
                                'status'=>'succeed',
                                'fromUser'=>$userInfo,
                                'at'=>[]
                            ];
                            Message::sendMessage($msg);
                        }
                    }
                }
            }
            $autoAddGroup=$chatInfo['autoAddGroup'] ?? [];
            // 是否自动加入群聊
            if($autoAddGroup && $autoAddGroup['status']==1){
                $group_id=$autoTask['group_id']??0;
                $uid=$autoAddGroup['owner_uid'] ?? 0;
                // 未设置群主就不能生成群
                if($uid){
                    $userInfo=$user->field('user_id,realname,avatar')->where(['user_id'=>$uid])->find();
                    $groupInfo=Group::where(['group_id'=>$group_id])->find();
                    // 如果没有群ID就需要创建
                    if(!$groupInfo){
                        $groupInfo=$this->createGroup($autoAddGroup,$userInfo,$autoTask['group_num']);
                    }
                    $groupUserCount=GroupUser::where(['group_id'=>$group_id,'status'=>1])->count();
                    if($groupUserCount > ($autoAddGroup['userMax'] ?? 100) - 1 ){
                        // 创建下一个群聊
                        $groupInfo=$this->createGroup($autoAddGroup,$userInfo,++$autoTask['group_num']);
                    }
                    $groupInfo['joinerName']=$data['realname'];
                    // 进入群聊并发送通知
                    GroupUser::joinGroup($data['user_id'],$uid,$groupInfo,'autoAddGroup');
                    // 记录本次的群聊ID
                    $autoTask['group_id']=$groupInfo['group_id'];
                }
            }
            Config::update(['value'=>$autoTask],['name'=>'autoTask']);
            return true;
        }catch(\Exception $e){
            return shutdown($e->getMessage().$e->getLine());
        }

    }

    // 创建配置文件
    public function createConf(){
        $autoTask=Config::where(['name'=>'autoTask'])->value('value');
        if($autoTask){
            return $autoTask;
        }else{
            $autoTask=[
                'group_id'=>0, //群聊ID
                'group_num'=>1, //群聊序号
                'user_id'=>0,  //上一次的客服ID
            ];
            Config::create(['name'=>'autoTask','value'=>$autoTask]);
            return $autoTask;
        }
    }

    // 查找ID的下一个值，如果未找到则使用第一个ID  
    public function findNextOrFirstId($ids, $searchId) { 
        if(!$ids){
            return 0;
        }
        // 遍历数组查找$searchId  
        foreach ($ids as $k => $v) {  
            // 如果找到了$searchId，则返回它的下一个值（如果存在）  
            if ($v == $searchId && isset($ids[$k + 1])) {  
                return $ids[$k + 1];  
            }  
        }  
        // 如果未找到$searchId，则返回第一个元素  
        return $ids[0];
    }  

    // 自动创建群聊
    public function createGroup($autoAddGroup,$userInfo,$group_num){ 
        $data=[
            'create_user'=>$userInfo['user_id'],
            'owner_id'=>$userInfo['user_id'],
            'name'=>($autoAddGroup['name'] ?? lang('group.name')).$group_num,
            'name_py'=>pinyin_sentence($autoAddGroup['name'].$group_num),
            'setting'=>json_encode(['manage' => 0, 'invite' => 1, 'nospeak' => 0]),
         ];
         $group=new Group;
         $group->save($data);
        //  群主首先进群
         $group->joinerName=$userInfo['realname'];
         GroupUser::joinGroup($userInfo['user_id'],$userInfo['user_id'],$group,'autoCreateGroup');
         return $group;
    }
}