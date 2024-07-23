<?php

namespace app\enterprise\controller;

use app\BaseController;
use app\enterprise\model\{User,Group as GroupModel,GroupUser,Message};
use think\Exception;
use think\facade\Db;
use app\common\controller\Upload;
use utils\Str;

class Group extends BaseController
{

   protected $setting=['manage' => 0, 'invite' => 1, 'nospeak' => 0];
      // 获取联系人列表
   public function getAllUser(){
      $param=$this->request->param();
      $user_ids=isset($param['user_ids'])?$param['user_ids']:[];
      $groupId=$param['group_id'] ?? '';
      $group_id='';
      if($groupId){
         $group_id=explode('-',$groupId)[1];
      }
      $data=User::getAllUser([['status','=',1],['user_id','<>',$this->userInfo['user_id']]],$user_ids,$this->uid,$group_id);
      return success('',$data);
   }

   // 获取群成员
   public function groupUserList()
   {
      $param = $this->request->param();
      try {
         $group_id = explode('-', $param['group_id'])[1];
         $listRows = $this->request->param('limit',0);
         $pageSize = $this->request->param('page',1);
         $map=['group_id' => $group_id];
         if($listRows){
            $list=GroupUser::where($map)->order('role asc')->paginate(['list_rows'=>$listRows,'page'=>$pageSize]);
            $data=$list->toArray()['data'];
            $count=$list->total();
         }else{
            $data=GroupUser::where($map)->order('role asc')->select();
            $count=count($data);
         }
         $data =User::matchAllUser($data,true,'user_id');
         return success('', $data,$count);
      } catch (Exception $e) {
         return error($e->getMessage());
      }
   }

   // 获取群基本信息
   public function groupInfo()
   {
      $param = $this->request->param();
      try {
         $jm='qr';
         $groupId=$param['group_id'] ?? '';
         $groupInfo = explode('-', $groupId);
         $group_id=$groupInfo[1];
         $group=GroupModel::find($group_id)->toArray();
         $userList=User::matchUser($group,false,'owner_id');
         $userCount=GroupUser::where(['group_id'=>$group_id])->count();
         $userInfo=$userList[$group['owner_id']];
         $expire=time()+7*86400;
         $token=urlencode(authcode($this->uid.'-'.$group_id,'ENCODE', $jm,7*86400));
         $qrUrl=request()->domain().'/scan/g/'.$token;
         $group['id']=$groupId;
         $group['qrUrl']=$qrUrl;
         $group['qrExpire']=date('m月d日',$expire);
         $group['userInfo']=$userInfo;
         $group['ownerName']=$userInfo['realname'];
         $group['groupUserCount']=$userCount;
         $group['displayName']=$group['name'];
         $group['avatar']=avatarUrl($group['avatar'],$group['name'],$group['group_id'],120);
         $group['setting']=$group['setting']?json_decode($group['setting'],true):['manage' => 0, 'invite' => 1, 'nospeak' => 0];
         $group['isJoin']=GroupUser::where(['group_id'=>$group_id,'user_id'=>$this->uid])->value('role') ?: 0;
         return success('', $group);
      } catch (Exception $e) {
         return error($e->getMessage());
      }
   }

   // 修改团队名称
   public function editGroupName()
   {
      $param = $this->request->param();
      $group_id = explode('-', $param['id'])[1];
      $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$this->userInfo['user_id']])->value('role');
      if($role>2){
         return warning(lang('group.notAuth'));
      }
      GroupModel::where(['group_id' => $group_id])->update(['name' => $param['displayName'],'name_py'=>pinyin_sentence($param['displayName'])]);
      $param['editUserName'] = $this->userInfo['realname'];
      $action='editGroupName';
      event('GroupChange', ['action' => $action, 'group_id' => $group_id, 'param' => $param]);
      wsSendMsg($group_id, $action, $param, 1);
      return success(lang('system.editOk'));
   }

   // 添加群成员
   public function addGroupUser(){
      $param = $this->request->param();
      $uid=$this->userInfo['user_id'];
      $group_id = explode('-', $param['id'])[1];
      $user_ids=$param['user_ids'];
      $groupUserCount=GroupUser::where(['group_id'=>$group_id,'status'=>1])->count();
      if((count($user_ids) + $groupUserCount) > $this->chatSetting['groupUserMax'] && $this->chatSetting['groupUserMax']!=0){
         return warning(lang('group.userLimit',['userMax'=>$this->chatSetting['groupUserMax']]));
      }
      $data=[];
      try{
         foreach($user_ids as $k=>$v){
            $data[]=[
               'group_id'=>$group_id,
               'user_id'=>$v,
               'role'=>3,
               'invite_id'=>$uid
            ];
         }
         $groupUser=new GroupUser;
         $groupUser->saveAll($data);
         $url=GroupModel::setGroupAvatar($group_id);
         // 更新原来群聊的头像和成员列表
         wsSendMsg($group_id,"addGroupUser",['group_id'=>$param['id'],'avatar'=>$url],1);
         // 给新成员添加新群聊信息
         $groupInfo=GroupModel::find($group_id);
         $groupInfo['displayName']=$groupInfo['name'];
         $groupInfo['role']=3;
         $groupInfo['id']='group-'.$group_id;
         $groupInfo['avatar']=avatarUrl($url,$groupInfo['name'],$group_id,120);
         $groupInfo['is_group']=1;
         $groupInfo['lastContent']=lang('group.invite',['username'=>$this->userInfo['realname']]);
         $groupInfo['lastSendTime']=time()*1000;
         $groupInfo['index']="[2]".lang('group.name');
         $groupInfo['is_notice']=1;
         $groupInfo['is_top']=0;
         $groupInfo['type']="text";
         wsSendMsg($user_ids, 'addGroup', $groupInfo);
         return success(lang('system.addOk'));
      }catch(Exception $e){
         return error($e->getMessage());
      }
   }

      // 设置管理员
      public function setManager(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         $user_id=$param['user_id'];
         $role=$param['role'];
         if(!GroupUser::checkAuth(['group_id'=>$group_id,'user_id'=>$uid])){
            return warning(lang('system.notAuth'));
         }
         $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
         if($groupUser){
            $groupUser->role=$role;
            $groupUser->save();
            $avatar=GroupModel::where(['group_id'=>$group_id])->value('avatar');
            $url=avatarUrl($avatar);
            wsSendMsg($group_id,"setManager",['group_id'=>$param['id'],'user_id'=>$user_id,'avatar'=>$url],1);
            return success(lang('system.settingOk'));
         }else{
            return warning('');
         }
         
      }

      // 添加群聊
      public function add(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $user_ids=$param['user_ids'] ?? [];
         if($this->chatSetting['groupChat']==0){
            return warning(lang('system.notAuth'));
         }
         if(count($user_ids)>$this->chatSetting['groupUserMax'] && $this->chatSetting['groupUserMax']!=0){
            return warning(lang('group.userLimit',['userMax'=>$this->chatSetting['groupUserMax']]));
         }
         // 管理员可以单独创建一个人的群
         if(count($user_ids)<=1 && $this->userInfo['role']>=2){
            return warning(lang('group.atLeast'));
         }
         // 将自己也加入群聊
         $user_ids[]=$uid;
         Db::startTrans();
         $setting=$this->setting;
         try{
            $create=[
               'create_user'=>$uid,
               'owner_id'=>$uid,
               'name'=>lang('group.name'),
               'name_py'=>"qunliao",
               'setting'=>json_encode($setting),
            ];
            $name=$param['name'] ?? '';
            if($name){
               $create['name']=$name;
               $create['name_py']=pinyin_sentence($name);
            }
            $group=new GroupModel();
            $group->save($create);
            $group_id=$group->group_id;
            $data=[];
            array_unique($user_ids);
            sort($user_ids);
            foreach($user_ids as $k=>$v){
               $info=[
                  'user_id'=>$v,
                  'invite_id'=>$uid,
                  'status'=>1,
                  'role'=>3,
                  'group_id'=>$group_id
               ];
               if($v==$uid){
                  $info['invite_id']=0;
                  $info['role']=1;
               }
               $data[]=$info;
            }
            $groupUser=new GroupUser();
            $groupUser->saveAll($data);
            $url=GroupModel::setGroupAvatar($group_id);
            $groupInfo=[
               'displayName'=>$create['name'],
               'owner_id'=>$create['owner_id'],
               'role'=>3,
               'name_py'=>$create['name_py'],
               'id'=>'group-'.$group_id,
               'avatar'=>avatarUrl($url,$create['name'],$group_id,120),
               'is_group'=>1,
               'lastContent'=>lang('group.add',['username'=>$this->userInfo['realname']]),
               'lastSendTime'=>time()*1000,
               'index'=>"[2]".lang('group.name'),
               'is_notice'=>1,
               'is_top'=>0,
               'setting'=>$setting,
         
            ];
            Message::create([
               'from_user'=>$uid,
               'to_user'=>$group_id,
               'content'=>str_encipher(lang('group.add',['username'=>''])),
               'type'=>'event',
               'is_group'=>1,
               'is_read'=>1,
               'is_last'=>1,
               'chat_identify'=>'group-'.$group_id
            ]);
            wsSendMsg($user_ids, 'addGroup', $groupInfo);
            Db::commit();
            $groupInfo['role']=1;
            return success('',$groupInfo);
         }catch(Exception $e){
            Db::rollback();
            return error($e->getMessage());
         }
      }

      // 移除成员
      public function removeUser(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         $user_id=$param['user_id'];
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         if($role>2 && $user_id!=$uid){
            return warning(lang('system.notAuth'));
         }
         $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
         if(($groupUser && $groupUser['role']>$role) || $user_id==$uid){
            GroupUser::destroy($groupUser->id);
         }else{
            return warning(lang('system.notAuth'));
         }
         $url=GroupModel::setGroupAvatar($group_id);
         wsSendMsg($group_id,"removeUser",['group_id'=>$param['id'],'avatar'=>$url,'user_id'=>$user_id],1);
         return success(lang('system.delOk'));
      }

      // 解散团队
      public function removeGroup(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         if($role>1){
            return warning(lang('system.notAuth'));
         }
         Db::startTrans();
         try{
            // 删除团队成员
            GroupUser::where(['group_id'=>$group_id])->delete();
            // 删除团队
            GroupModel::destroy($group_id);
            wsSendMsg($group_id,"removeGroup",['group_id'=>$param['id']],1);
            Db::commit();
            return success('');
         }catch(Exception $e){
            Db::rollback();
            return error($e->getMessage());
         }
      }

      // 设置公告
      public function setNotice(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         // 公告内容检测服务
         event('GreenText',['content'=>$param['notice'],'service'=>"comment_detection"]);
         $group_id = explode('-', $param['id'])[1];
         if($param['notice']==''){
            return warning(lang('system.notNull'));
         }
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         if($role>2){
            return warning(lang('system.notAuth'));
         }
         GroupModel::update(['notice'=>$param['notice']],['group_id'=>$group_id]);
         $msg=[
            'id'=>\utils\Str::getUuid(),
            'user_id'=>$uid,
            'content'=>'<b>'.lang('group.notice').'：</b>&nbsp;@'.lang('group.all').'<br/>'.$param['notice'].'<br/>',
            'toContactId'=>$param['id'],
            'sendTime'=>time()*1000,
            'type'=>'text',
            'is_group'=>1,
            'status'=>'succeed',
            'fromUser'=>$this->userInfo,
            'at'=>[0]
        ];
         Message::sendMessage($msg);
         // wsSendMsg($group_id,"setNotice",['group_id'=>$param['id'],'notice'=>$param['notice']],1);
         return success('');
      }

      // 群聊设置
      public function groupSetting(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         if($role!=1){
            return warning(lang('system.notAuth'));
         }
         $setting=json_encode($param['setting']);
         GroupModel::update(['setting'=>$setting],['group_id'=>$group_id]);
         wsSendMsg($group_id,"groupSetting",['group_id'=>$param['id'],'setting'=>$param['setting']],1);
         return success('');
      }

      //生成群聊头像
      protected function setGroupAvatar($group_id){
         $userList=GroupUser::where('group_id',$group_id)->limit(9)->column('user_id');
         $userList=User::where('user_id','in',$userList)->select()->toArray();
         $imgList=[];
         $dirPath=app()->getRootPath().'public/temp';
         foreach($userList as $k=>$v){
            if($v['avatar']){
               $imgList[]=avatarUrl($v['avatar'],$v['realname'],$v['user_id']);
            }else{
               $imgList[]=circleAvatar($v['realname'],80,$v['user_id'],1,$dirPath);
            }
         }
         $groupId='group_'.$group_id;
         $path=$dirPath.'/'.$groupId.'.jpg';
         $a = getGroupAvatar($imgList,1,$path);
         $url='';
         if($a){
            $upload=new Upload();
            $newPath=$upload->uploadLocalAvatar($path,[],$groupId);
            if($newPath){
               GroupModel::where('group_id',$group_id)->update(['avatar'=>$newPath]);
               $url=avatarUrl($newPath);
            }
         }
         // 删除目录下的所有文件
         $files = glob($dirPath . '/*'); // 获取目录下所有文件路径
         foreach ($files as $file) {
            if (is_file($file)) { // 如果是文件则删除
               unlink($file);
            }
         }
         return $url;
      }

      // 加入群
      public function joinGroup(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         try{
            $group_id = explode('-', $param['group_id'])[1];
            $inviteUid=$param['inviteUid'] ?? '';
            $groupUserCount=GroupUser::where(['group_id'=>$group_id,'status'=>1])->count();
            $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->find();
            $groupInfo=GroupModel::where(['group_id'=>$group_id])->find();
            if(!$groupInfo){
               return warning(lang('group.exist'));
            }
            if($groupUser){
               return warning(lang('group.alreadyJoin'));
            }
            if(($groupUserCount+1) > $this->chatSetting['groupUserMax'] && $this->chatSetting['groupUserMax']!=0){
               return warning(lang('group.userLimit',['userMax'=>$this->chatSetting['groupUserMax']]));
            }
            // 加入者的名称
            $groupInfo['joinerName']=$this->userInfo['realname'];
            GroupUser::joinGroup($uid,$inviteUid,$groupInfo);
            return success(lang('group.joinOk'));
         }catch(Exception $e){
            return error($e->getMessage());
         }
      }

   // 更换群主
    public function changeOwner()
    {
        $user_id = $this->request->param('user_id');
        $id = $this->request->param('id');
        $group_id = explode('-', $id)[1];
        $uid=$this->userInfo['user_id'];
        $group=GroupModel::where('group_id',$group_id)->find();
        if(!$group){
            return warning(lang('group.exist'));
        }
        $user=User::where('user_id',$user_id)->find();
        if(!$user){
            return warning(lang('user.exist'));
        }
        $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
        if($role>1){
           return warning(lang('system.notAuth'));
        }
        Db::startTrans();
        try{
            GroupUser::where('group_id',$group_id)->where('user_id',$user_id)->update(['role'=>1]);
            GroupUser::where('group_id',$group_id)->where('user_id',$group->owner_id)->update(['role'=>3]);
            $group->owner_id=$user_id;
            $group->save();
            wsSendMsg($group_id,"changeOwner",['group_id'=>'group-'.$group_id,'user_id'=>$user_id],1);
            Db::commit();
            return success('');
        }catch (\Exception $e){
            Db::rollback();
            return warning('');
        }
    }

      // 清理群消息
      public function clearMessage()
      {
         $id = $this->request->param('id');
         $group_id = explode('-', $id)[1];
         $uid=$this->userInfo['user_id'];
         $group=GroupModel::where('group_id',$group_id)->find();
         if(!$group){
            return warning(lang('group.exist'));
         }
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         // 如果是群主或者后台管理员才有权限
         if($role>1 && $this->userInfo['role']==0){
            return warning(lang('system.notAuth'));
         }
         Db::startTrans();
         try{
            // 删除所有消息
            Message::where(['chat_identify'=>$id])->delete();
            // 该群聊的所有未读置为0
            GroupUser::where('group_id',$group_id)->update(['unread'=>0]);
            wsSendMsg($group_id,"clearMessage",['group_id'=>'group-'.$group_id],1);
            Db::commit();
            return success('');
         }catch (\Exception $e){
            Db::rollback();
            return warning('');
         }
      }
}
