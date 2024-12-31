<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\enterprise\model;

use app\BaseModel;
use think\facade\Db;

class GroupUser extends BaseModel
{
    protected $pk="id";

   // 编辑团队信息
   public static function editGroupUser($map,$data){
      return self::where($map)->update($data);
   }

   // 获取团队成员列表
   public static function getGroupUser($map,$listRows,$pageSize=1){
      if($listRows){
         $list=self::where($map)->order('role asc')->paginate(['list_rows'=>$listRows,'page'=>$pageSize]);
         $data=$list->toArray()['data'];
      }else{
         $data=self::where($map)->order('role asc')->select();
      }
      return User::matchAllUser($data,true,'user_id');
   }

   // 验证权限
   public static function checkAuth($map,$role=1){
      $info=self::where($map)->find()->toArray();
      if($info['role']<=$role){
         return true;
      }else{
         return false;
      }
   }

   // 加入群聊，发送加入消息
   public static function joinGroup($uid,$inviteId,$groupInfo,$action='joinGroup'){
      $group_id=$groupInfo['group_id'];
      GroupUser::create([
         'user_id'=>$uid,
         'invite_id'=>$inviteId,
         'status'=>1,
         'role'=>$action=='autoCreateGroup' ? 1 : 3,
         'group_id'=>$group_id,
      ]);
      $url=Group::setGroupAvatar($group_id);
      event('GroupChange', ['action' => $action, 'group_id' => $group_id, 'param' => $groupInfo]);
      wsSendMsg($group_id,"addGroupUser",['group_id'=>$group_id,'avatar'=>$url],1);
      return true;
   }

   // 获取群管理
   public static function getGroupManage($group_id){
      $list=self::where([['group_id','=',$group_id],['role','<',3],['status','=',1]])->select()->toArray();
      $data=[];
      foreach($list as $k=>$v){
         $data[$v['user_id']]=$v['role'];
      }
      return $data;
   }
}