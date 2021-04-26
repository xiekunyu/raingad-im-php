<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\model;

use think\Model;
use think\facade\Db;

class Group extends Model
{
    protected $pk="group_id";

   // 获取我的团队
   public static function getMyGroup($map){
      return Db::name('group_user')
      ->alias('gu')
      ->field('gr.group_id,gr.name as displayName,gu.unread,gr.name_py')
      ->join('group gr','gu.group_id=gr.group_id','left')
      ->where($map)
      ->select();
   }

   // 获取我的团队
   public static function editGroupUser($map,$data){
      return Db::name('group_user')->where($map)->update($data);
   }

   

}