<?php

/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */

namespace app\enterprise\model;

use GatewayClient\Gateway;
use think\Model;
use think\facade\Db;
use think\facade\Request;

class User extends Model
{
   protected $pk = "user_id";
   protected static $defaultField = 'user_id,realname,account,avatar,name_py';

   public static $user_id = '';

   public static $user_info = [];

   // 模型初始化
   protected static function init()
   {
      //TODO:初始化内容
      $request = Request::instance();
      self::$user_id = $request->userInfo['user_id'];
      self::$user_info = $request->userInfo;
   }
   //查询用户信息
   public static function getUserInfo($map)
   {
      $data = self::where($map)->find();
      if ($data) {
         $data = $data->toArray();
      }
      return $data;
   }
   //   获取所有用户列表
   public static function getAllUser($map, $user_ids = [], $field = 'user_id,realname,avatar,account,name_py')
   {
      $list = self::where($map)->field($field)->select()->toArray();
      foreach ($list as $k => $v) {
         $list[$k]['disabled'] = false;
         $list[$k]['avatar'] = avatarUrl($v['avatar'], $v['realname'], $v['user_id']);
         if ($user_ids) {
            if (in_array($v['user_id'], $user_ids)) {
               $list[$k]['disabled'] = true;
            }
         }
      }
      return $list;
   }

   //查询用户列表
   public static function getUserList($map, $user_id, $field = "")
   {
      if (!$field) {
         $field = self::$defaultField;
      }
      $list = Db::name('user')->field($field)->where($map)->select();
      $list_chart = chartSort($list, 'realname', false, 'index');
      $friendList = Friend::getFriend(['create_user' => $user_id]);
      // 查询未读消息
      $unread = Db::name('message')
         ->field('from_user,count(msg_id) as unread')
         ->where([['to_user', '=', $user_id], ['is_read', '=', 0], ['is_group', '=', 0]])
         ->group('from_user')
         ->select();
      // 查询最近的联系人
      $map1 = [['to_user', '=', $user_id], ['is_last', '=', 1], ['is_group', '=', 0]];
      $map2 = [['from_user', '=', $user_id], ['is_last', '=', 1], ['is_group', '=', 0]];
      $msgField = 'from_user,to_user,content as lastContent,create_time as lastSendTime,chat_identify,type,del_user';
      $lasMsgList = Db::name('message')
         ->field($msgField)
         ->whereOr([$map1, $map2])
         ->select();
      // 查询群聊
      $group = Group::getMyGroup(['gu.user_id' => $user_id, 'gu.status' => 1]);
      if ($group) {
         $group = $group->toArray();
         $group_ids = arrayToString($group, 'group_id');
         $getGroupLastMsg = Db::name('message')->field($msgField)->where([['to_user', 'in', $group_ids], ['is_group', '=', 1], ['is_last', '=', 1]])->select();
         foreach ($group as $k => $v) {
            $setting = $v['setting'] ? json_decode($v['setting'], true) : ['manage' => 0, 'invite' => 1, 'nospeak' => 0];
            $group_id = 'group-' . $v['group_id'];
            $group[$k]['id'] = $group_id;
            $group[$k]['avatar'] = avatarUrl('', $v['displayName'], $v['group_id'], 120);
            $group[$k]['name_py'] = $v['name_py'];
            $group[$k]['owner_id'] = $v['owner_id'];
            $group[$k]['role'] = $v['role'];
            $group[$k]['is_group'] = 1;
            $group[$k]['setting'] = $setting;
            $group[$k]['index'] = "群聊";
            $group[$k]['realname'] = $v['displayName'] . " [群聊]";
            $group[$k]['is_notice'] = $v['is_notice'];
            $group[$k]['is_online'] = 1;
            // 是否置顶聊天
            $friend = isset($friendList[$group_id]) ? $friendList[$group_id] : [];
            $is_top = 0;
            if ($friend) {
               $is_top = $friend['is_top'];
            }
            $group[$k]['is_top'] = $is_top;
            if ($getGroupLastMsg) {
               foreach ($getGroupLastMsg as $val) {
                  if ($val['to_user'] == $v['group_id']) {
                     $group[$k]['type'] =$val['type'];
                     $group[$k]['lastContent'] = $val['lastContent'];
                     $group[$k]['lastSendTime'] = $val['lastSendTime'] * 1000;
                     break;
                  }
               }
            }
         }
      }
      $onlineList=Gateway::getAllUidList();
      foreach ($list_chart as $k => $v) {
         $list_chart[$k]['id'] = $v['user_id'];
         $list_chart[$k]['displayName'] = $v['realname'];
         $list_chart[$k]['name_py'] = $v['name_py'];
         $list_chart[$k]['avatar'] = avatarUrl($v['avatar'], $v['realname'], $v['user_id'], 120);
         $list_chart[$k]['lastContent'] = '';
         $list_chart[$k]['unread'] = 0;
         $list_chart[$k]['lastSendTime'] = time() * 1000;
         $list_chart[$k]['is_group'] = 0;
         $list_chart[$k]['setting'] = [];
         $is_online=0;
         if(isset($onlineList[$v['user_id']])){
            $is_online=1;
         }
         $list_chart[$k]['is_online'] = $is_online;
         // 是否置顶聊天
         $friend = isset($friendList[$v['user_id']]) ? $friendList[$v['user_id']] : [];
         $is_top = 0;
         $is_notice = 1;
         if ($friend) {
            $is_top = $friend['is_top'];
            $is_notice = $friend['is_notice'];
         }
         $list_chart[$k]['is_top'] = $is_top;
         $list_chart[$k]['is_notice'] = $is_notice;
         if ($unread) {
            foreach ($unread as $val) {
               if ($val['from_user'] == $v['user_id']) {
                  $list_chart[$k]['unread'] = $val['unread'];
                  break;
               }
            }
         }
         if ($lasMsgList) {
            foreach ($lasMsgList as $val) {
               if ($val['from_user'] == $v['user_id'] || $val['to_user'] == $v['user_id']) {
                  $content = $val['lastContent'];
                  // 屏蔽已删除的消息
                  if ($val['del_user']) {
                     $delUser = explode(',', $val['del_user']);
                     if (in_array($user_id, $delUser)) {
                        $content = "";
                     }
                  }
                  $list_chart[$k]['type'] = $val['type'];
                  $list_chart[$k]['lastContent'] = $content;
                  $list_chart[$k]['lastSendTime'] = $val['lastSendTime'] * 1000;

                  break;
               }
            }
         }
      }
      // 合并群聊和联系人
      $data = array_merge($list_chart, $group);
      return $data;
   }

   public static function getList($map)
   {
      return Db::name('user')->field('user_id,realname,account,avatar')->where($map)->select();
   }

   // 匹配用户列表信息(返回用户信息)

   public static function matchUser($data, $many = false, $field = 'user_id', $cs = 80)
   {
      if ($many) {
         $idr = arrayToString($data, $field, false);
      } else {
         $idr = [];
         if (is_array($field)) {
            foreach ($field as $v) {
               $idr[] = $data[$v];
            }
         } else {
            $idr = [$data[$field]];
         }
      }
      $key = array_search(0, $idr);
      if ($key) {
         array_splice($idr, $key, 1);
      }
      $userList = self::where([['user_id', 'in', $idr]])->field(self::$defaultField)->select()->toArray();
      $list = [];
      foreach ($userList as $v) {
         $v['avatar'] = avatarUrl($v['avatar'], $v['realname'], $v['user_id'], $cs);
         $v['id'] = $v['user_id'];
         $list[$v['user_id']] = $v;
      }
      return $list;
   }

   // 匹配用户列表信息（返回data）  
   public static function matchAllUser($data, $many = false, $field = 'user_id', $key = "userInfo", $cs = 80)
   {
      if ($many) {
         $idr = arrayToString($data, $field);
         $userList = self::getList([['user_id', 'in', $idr]]);
         foreach ($data as $k => $v) {
            foreach ($userList as $vv) {
               if ($v[$field] == $vv['user_id']) {
                  $data[$k][$key] = [
                     'id' => $vv['user_id'],
                     'displayName' => $vv['realname'],
                     'account' => $vv['account'],
                     'avatar' => avatarUrl($vv['avatar'], $vv['realname'], $vv['user_id'], $cs),
                  ];
               }
            }
         }
      } else {
         $user = self::getUserInfo(['user_id' => $data[$field]]);
         $data[$key] = [
            'id' => $user['user_id'],
            'displayName' => $user['realname'],
            'account' => $user['account'],
            'avatar' => avatarUrl($user['avatar'], $user['realname'], $user['user_id']),
         ];
      }
      return $data;
   }
}
