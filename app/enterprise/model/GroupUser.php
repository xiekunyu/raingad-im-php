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
   public static function getGroupUser($map){
      $data=self::where($map)->order('role asc')->select();
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
}