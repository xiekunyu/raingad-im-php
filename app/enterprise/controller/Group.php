<?php

namespace app\enterprise\controller;

use app\BaseController;
use app\enterprise\model\{User,Group as GroupModel,GroupUser};
use think\Exception;
use think\facade\Db;
use app\common\controller\Upload;

class Group extends BaseController
{

   protected $setting=['manage' => 0, 'invite' => 1, 'nospeak' => 0];
      // 获取联系人列表
   public function getAllUser(){
      $param=$this->request->param();
      $user_ids=isset($param['user_ids'])?$param['user_ids']:[];
      $data=User::getAllUser([['status','=',1],['isdelete','=',0],['user_id','<>',$this->userInfo['user_id']]],$user_ids);
      return success('',$data);
   }

   // 获取群成员
   public function groupUserList()
   {
      $param = $this->request->param();
      try {
         $group_id = explode('-', $param['group_id'])[1];
         $data = GroupUser::getGroupUser(['group_id' => $group_id]);
         return success('', $data);
      } catch (Exception $e) {
         return error($e->getMessage());
      }
   }

   // 获取群基本信息
   public function groupInfo()
   {
      $param = $this->request->param();
      try {
         $group_id = explode('-', $param['group_id'])[1];
         $group=GroupModel::find($group_id)->toArray();
         $userList=User::matchUser($group,false,'owner_id');
         $userCount=GroupUser::where(['group_id'=>$group_id])->count();
         $userInfo=$userList[$group['owner_id']];
         $group['userInfo']=$userInfo;
         $group['ownerName']=$userInfo['realname'];
         $group['groupUserCount']=$userCount;
         $group['setting']=$group['setting']?json_decode($group['setting'],true):['manage' => 0, 'invite' => 1, 'nospeak' => 0];
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
         return warning('你没有操作权限，只有群主和群管理员才可以修改！');
      }
      GroupModel::where(['group_id' => $group_id])->update(['name' => $param['displayName'],'name_py'=>pinyin_sentence($param['displayName'])]);
      $param['editUserName'] = $this->userInfo['realname'];
      wsSendMsg($group_id, 'editGroupName', $param, 1);
      return success('修改成功');
   }

   // 添加群成员
   public function addGroupUser(){
      $param = $this->request->param();
      $uid=$this->userInfo['user_id'];
      $group_id = explode('-', $param['id'])[1];
      $user_ids=$param['user_ids'];
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
         $url=$this->setGroupAvatar($group_id);
         wsSendMsg($group_id,"addGroupUser",['group_id'=>$param['id'],'avatar'=>$url],1);
         return success('添加成功');
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
            return warning('您没有操作权限！');
         }
         $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
         if($groupUser){
            $groupUser->role=$role;
            $groupUser->save();
            wsSendMsg($group_id,"setManager",['group_id'=>$param['id']],1);
            return success('设置成功');
         }else{
            return warning('设置失败！');
         }
         
      }

      // 添加群聊
      public function add(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $user_ids=$param['user_ids'];
         if(count($user_ids)<=1){
            return warning("请至少选择两人！");
         }
         // 将自己也加入群聊
         $user_ids[]=$this->userInfo['user_id'];
         Db::startTrans();
         $setting=$this->setting;
         try{
            $create=[
               'create_user'=>$uid,
               'owner_id'=>$uid,
               'name'=>"群聊",
               'name_py'=>"qunliao",
               'setting'=>json_encode($setting),
            ];
            $group=new GroupModel();
            $group->save($create);
            $group_id=$group->group_id;
            $data=[];
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
            $url=$this->setGroupAvatar($group_id);
            $groupInfo=[
               'displayName'=>$create['name'],
               'owner_id'=>$create['owner_id'],
               'role'=>3,
               'name_py'=>$create['name_py'],
               'id'=>'group-'.$group_id,
               'avatar'=>avatarUrl($url,'群聊',$group_id,120),
               'is_group'=>1,
               'lastContent'=>$this->userInfo['realname'].' 创建了群聊',
               'lastSendTime'=>time()*1000,
               'index'=>"群聊",
               'is_notice'=>1,
               'is_top'=>0,
               'setting'=>$setting,
         
            ];
            
            wsSendMsg($user_ids, 'addGroup', $groupInfo);
            Db::commit();
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
            return warning('您没有操作权限！');
         }
         $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
         if(($groupUser && $groupUser['role']>$role) || $user_id==$uid){
            GroupUser::destroy($groupUser->id);
         }else{
            return warning('您的权限不够！');
         }
         $url=$this->setGroupAvatar($group_id);
         wsSendMsg($group_id,"removeUser",['group_id'=>$param['id'],'avatar'=>$url],1);
         return success('删除成功');
      }

      // 解散团队
      public function removeGroup(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         if($role>1){
            return warning('您没有操作权限！');
         }
         Db::startTrans();
         try{
            // 删除团队成员
            GroupUser::where(['group_id'=>$group_id])->delete();
            // 删除团队
            GroupModel::destroy($group_id);
            wsSendMsg($group_id,"removeGroup",['group_id'=>$param['id']],1);
            Db::commit();
            return success();
         }catch(Exception $e){
            Db::rollback();
            return error($e->getMessage());
         }
      }

      // 设置公告
      public function setNotice(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         if($param['notice']==''){
            return warning('请输入内容！');
         }
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         // if($role>2){
         //    return warning('您没有操作权限！');
         // }
         GroupModel::update(['notice'=>$param['notice']],['group_id'=>$group_id]);
         wsSendMsg($group_id,"setNotice",['group_id'=>$param['id'],'notice'=>$param['notice']],1);
         return success('');
      }

      // 群聊设置
      public function groupSetting(){
         $param = $this->request->param();
         $uid=$this->userInfo['user_id'];
         $group_id = explode('-', $param['id'])[1];
         $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$uid])->value('role');
         if($role!=1){
            return warning('您没有操作权限！');
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
}
