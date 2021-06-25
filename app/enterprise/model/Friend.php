<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\enterprise\model;

use think\Model;
use think\facade\Db;

class Friend extends Model
{
    protected $pk="friend_id";
    

    public static function getFriend($map){
       $list=self::where($map)->select();
       $data=[];
       if($list){
          $list=$list->toArray();
          foreach($list as $k=>$v){
             $data[$v['friend_user_id']]=$v;
          }
       }
       return $data;
    }
   
}