<?php
/**
 * Created by PhpStorm
 * User raingad@foxmail.com
 * Date 2022/12/14 17:24
 */
namespace app\manage\controller;
use app\BaseController;
use app\enterprise\model\{User as UserModel,GroupUser,Friend};
use think\facade\Db;

class User extends BaseController
{
    // 获取用户列表
    public function index()
    {
        $map = [];
        $model=new UserModel();
        $param = $this->request->param();
        //搜索关键词
        if ($keyword = $this->request->param('keywords')) {
            $model = $model->whereLike('realname|account|name_py|email', '%' . $keyword . '%');
        }
        // 排序
        $order='user_id DESC';
        if ($param['order_field'] ?? '') {
            $order = orderBy($param['order_field'],$param['order_type'] ?? 1);
        }
        $list = $this->paginate($model->where($map)->order($order));
        if ($list) {
            $data = $list->toArray()['data'];
            foreach($data as $k=>$v){
                $data[$k]['avatar']=avatarUrl($v['avatar'],$v['realname'],$v['user_id'],120);
                unset($data[$k]['password']);
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }

    // 添加用户
    public function add()
    {
        if($this->request->demonMode){
            return warning('演示模式下无法操作！');
        }
        try{
            $data = $this->request->param();
            $user=UserModel::where('account',$data['account'])->find();
            if($user){
                return warning('账户已存在');
            }
            // 验证账号是否为手机号或者邮箱
            if(!\utils\Regular::is_email($data['account']) && !\utils\Regular::is_phonenumber($data['account'])){
                return warning('账户必须为手机号或者邮箱');
            }
            $salt=\utils\Str::random(4);
            $data['password'] = password_hash_tp($data['password'],$salt);
            $data['salt'] =$salt;
            $data['name_py'] = pinyin_sentence($data['realname']);
            $user=new UserModel();
            $user->save($data);
            $data['user_id']=$user->user_id;
            return success('添加成功', $data);
        }catch (\Exception $e){
            return error('添加失败');
        }
    }

    // 修改用户    
    public function edit()
    {
        if($this->request->demonMode){
            return warning('演示模式下无法操作！');
        }
        try{
            $data = $this->request->param();
            // 验证账号是否为手机号或者邮箱
            if(!\utils\Regular::is_email($data['account']) && !\utils\Regular::is_phonenumber($data['account'])){
                return warning('账户必须为手机号或者邮箱');
            }
            $user=UserModel::find($data['user_id']);
            if(!$user){
                return warning('用户不存在');
            }
            if($user->user_id==1 && $this->userInfo['user_id']!=1){
                return warning('超管账户只有自己才能修改');
            }
            $other=UserModel::where([['account','=',$data['account']],['user_id','<>',$data['user_id']]])->find();
            if($other){
                return warning('账户已存在');
            }
            $user->account =$data['account'];
            $user->realname =$data['realname'];
            $user->email =$data['email'];
            $user->remark=$data['remark'];
            $user->sex =$data['sex'];
            // 只有超管才能设置管理员
            if($this->userInfo['user_id']==1){
                $user->role =$data['role'];
            }
            $user->status =$data['status'];
            $user->name_py= pinyin_sentence($data['realname']);
            $user->save();
            return success('修改成功', $data);
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

    // 删除用户
    public function del()
    {
        if($this->request->demonMode){
            return warning('演示模式下无法操作！');
        }
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return warning('用户不存在');
        }
        Db::startTrans();
        try{
            // 删除其好友关系
            Friend::where('create_user', $user_id)->whereOr(['friend_user_id'=>$user_id])->delete();
            // 删除其群组关系
            GroupUser::where('user_id', $user_id)->delete();
            UserModel::destroy($user_id);
            Db::commit();
            return success('删除成功');
        }catch (\Exception $e){
            Db::rollback();
            return error($e->getMessage());
        }
    }

    // 修改用户状态
    public function setStatus()
    {
        if($this->request->demonMode){
            return warning('演示模式下无法操作！');
        }
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return warning('用户不存在');
        }
        try{
            $status = $this->request->param('status',0);
            UserModel::where('user_id', $user_id)->update(['status'=>$status]);
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

    // 获取用户信息
    public function detail()
    {
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error('用户不存在');
        }
        $user->avatar=avatarUrl($user->avatar,$user->realname,$user->user_id,120);
        $location='';
        if($user->last_login_ip){
            $location=implode(" ", \Ip::find($user->last_login_ip));
        }
        $user->location=$location;
        $user->password='';
        return success('', $user);
    }

    // 设置用户角色
    public function setRole()
    {
        if($this->request->demonMode){
            return warning('演示模式下无法操作！');
        }
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return warning('用户不存在');
        }
        try{
            $role = $this->request->param('role');
            UserModel::where('user_id', $user_id)->update(['role'=>$role]);
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

    // 修改密码
    public function editPassword()
    {
        if($this->request->demonMode){
            return warning('演示模式下无法操作！');
        }
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return warning('用户不存在');
        }
        try{
            $password = $this->request->param('password','');
            if($password){
                $salt=$user->salt;
                $user->password= password_hash_tp($password,$salt);
            }
            $user->save();
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

}