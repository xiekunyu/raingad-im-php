<?php

namespace app\controller;

use app\BaseController;
use app\model\{User,Group as GroupModel,GroupUser};
use think\Exception;
use think\facade\View;
use think\facade\Session;
use think\facade\Db;
use think\facade\Cache;
use think\exception\ValidateException;

class Group extends BaseController
{

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

   // 修改团队名称
   public function editGroupName()
   {
      $param = $this->request->param();
      $group_id = explode('-', $param['id'])[1];
      $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$this->userInfo['user_id']])->value('role');
      if($role>2){
         return warning('你没有操作权限，只有群主和群管理员才可以修改！');
      }
      GroupModel::where(['group_id' => $group_id])->update(['name' => $param['displayName']]);
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
         wsSendMsg($group_id,"addGroupUser",['group_id'=>$param['id']],1);
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
         try{
            $create=[
               'create_user'=>$uid,
               'owner_id'=>$uid,
               'name'=>"群聊",
               'name_py'=>"qunliao",
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
            $groupInfo=[
               'displayName'=>$create['name'],
               'owner_id'=>$create['owner_id'],
               'name_py'=>$create['name_py'],
               'id'=>'group-'.$group_id,
               'avatar'=>'https://lvzhe-file.oss-cn-beijing.aliyuncs.com/tools/group.png',
               'is_group'=>1,
               'lastContent'=>'',
               'lastSendTime'=>''
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
         if($role>2){
            return warning('您没有操作权限！');
         }
         $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
         if($groupUser && $groupUser['role']>$role){
            GroupUser::destroy($groupUser->id);
         }else{
            return warning('您的权限不够！');
         }
         wsSendMsg($group_id,"removeUser",['group_id'=>$param['id']],1);
         return success('删除成功');
      }
}
