<?php

namespace app\controller;

use app\BaseController;
use app\model\{User,Group as GroupModel,GroupUser};
use Exception;
use think\facade\View;
use think\facade\Session;
use think\facade\Db;
use think\facade\Cache;
use think\exception\ValidateException;

class Group extends BaseController
{

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
}
