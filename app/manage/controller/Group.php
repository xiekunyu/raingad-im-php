<?php
/**
 * Created by PhpStorm
 * User raingad@foxmail.com
 * Date 2022/12/14 17:24
 */
namespace app\manage\controller;
use app\BaseController;
use app\enterprise\model\{User as UserModel,GroupUser,Group as GroupModel};
use think\facade\Db;

class Group extends BaseController
{
    // 获取群聊列表
    public function index()
    {
        $map = [];
        $model=new GroupModel();
        $param = $this->request->param();
        //搜索关键词
        if ($keyword = $this->request->param('keywords')) {
            $model = $model->whereLike('name|name_py', '%' . $keyword . '%');
        }
        // 排序
        $order='group_id DESC';
        if ($param['order_field'] ?? '') {
            $order = orderBy($param['order_field'],$param['order_type'] ?? 1);
        }
        $list = $this->paginate($model->where($map)->order($order));
        if ($list) {
            $data = $list->toArray()['data'];
            $userList=UserModel::matchUser($data,true,'owner_id',120);
            foreach($data as $k=>$v){
                $data[$k]['avatar']=avatarUrl($v['avatar'],$v['name'],$v['group_id'],120);
                $data[$k]['owner_id_info']=$userList[$v['owner_id']] ?? [];
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }

    // 更换群主
    public function changeOwner()
    {
        $group_id = $this->request->param('group_id');
        $user_id = $this->request->param('user_id');
        $group=GroupModel::where('group_id',$group_id)->find();
        if(!$group){
            return warning('群组不存在');
        }
        $user=UserModel::where('user_id',$user_id)->find();
        if(!$user){
            return warning('用户不存在');
        }
        Db::startTrans();
        try{
            GroupUser::where('group_id',$group_id)->where('user_id',$user_id)->update(['role'=>1]);
            GroupUser::where('group_id',$group_id)->where('user_id',$group->owner_id)->update(['role'=>3]);
            $group->owner_id=$user_id;
            $group->save();
            wsSendMsg($group_id,"changeOwner",['group_id'=>'group-'.$group_id,'user_id'=>$user_id],1);
            Db::commit();
            return success('保存成功');
        }catch (\Exception $e){
            Db::rollback();
            return warning('更换失败');
        }
    }

    // 解散群聊
    public function del()
    {
        $group_id = $this->request->param('group_id');
        $group=GroupModel::where('group_id',$group_id)->find();
        if(!$group){
            return warning('群组不存在');
        }
        Db::startTrans();
        try{
            // 删除团队成员
            GroupUser::where('group_id',$group_id)->delete();
            // 删除团队
            GroupModel::destroy($group_id);
            wsSendMsg($group_id,"removeGroup",['group_id'=>'group-'.$group_id],1);
            Db::commit();
            return success('解散成功');
        }catch (\Exception $e){
            Db::rollback();
            return warning('解散失败');
        }
    }

    // 添加群成员
    public function addGroupUser(){
        $param = $this->request->param();
        $uid=$this->userInfo['user_id'];
        $group_id = $param['group_id'];
        $group=GroupModel::where('group_id',$group_id)->find();
        if(!$group){
            return warning('群组不存在');
        }
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
            $url=GroupModel::setGroupAvatar($group_id);
            wsSendMsg($group_id,"addGroupUser",['group_id'=>"group-".$group_id,'avatar'=>$url],1);
            return success('添加成功');
        }catch(\Exception $e){
                return error($e->getMessage());
        }
        
    }

    // 删除群成员
    public function delGroupUser(){
        $param = $this->request->param();
        $group_id = $param['group_id'];
        $group=GroupModel::where('group_id',$group_id)->find();
        if(!$group){
            return warning('群组不存在');
        }
        $user_id=$param['user_id'];
        $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
        if($groupUser){
            $groupUser->delete();
            wsSendMsg($group_id,"removeUser",['group_id'=>'group-'.$group_id],1);
            return success('删除成功');
        }else{
            return warning('删除失败！');
        }
        
    }

    // 设置管理员
    public function setManager(){
       $param = $this->request->param();
       $group_id = $param['group_id'];
        $group=GroupModel::where('group_id',$group_id)->find();
        if(!$group){
            return warning('群组不存在');
        }
       $user_id=$param['user_id'];
       $role=$param['role'];
       $groupUser=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->find();
       if($groupUser){
          $groupUser->role=$role;
          $groupUser->save();
          wsSendMsg($group_id,"setManager",['group_id'=>'group-'.$group_id],1);
          return success('设置成功');
       }else{
          return warning('设置失败！');
       }
       
    }


}