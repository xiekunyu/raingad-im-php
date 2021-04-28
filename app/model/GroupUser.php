<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\model;

use think\Model;
use think\facade\Db;

class GroupUser extends Model
{
    protected $pk="id";

   // 编辑团队信息
   public static function editGroupUser($map,$data){
      return self::where($map)->update($data);
   }

   // 获取团队成员列表
   public static function getGroupUser($map){
      $data=self::where($map)->order('role asc')->select();
      return User::matchUser($data,true,'user_id');

   }

}