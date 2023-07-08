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

   //生成群聊头像
   public static function setGroupAvatar($group_id){
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
            Group::where('group_id',$group_id)->update(['avatar'=>$newPath]);
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