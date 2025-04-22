<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\enterprise\model;

use app\BaseModel;
use think\facade\Db;
use app\common\controller\Upload;
class Group extends BaseModel
{
    protected $pk="group_id";

   // 获取我的团队
   public static function getMyGroup($map){
      return Db::name('group_user')
      ->alias('gu')
      ->field('gr.group_id,gr.avatar,gr.name as displayName,gu.unread,gr.name_py,gr.owner_id,gr.notice,gu.role,gu.is_notice,gu.is_top,gr.setting')
      ->join('group gr','gu.group_id=gr.group_id','left')
      ->where($map)
      ->select();
   }

}