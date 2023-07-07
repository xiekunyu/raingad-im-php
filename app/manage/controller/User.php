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
        $map = [['status','=',1]];
        $model=new UserModel();
        
        //搜索关键词
        if ($keyword = $this->request->param('keywords')) {
            $model = $model->whereLike('realname|account|name_py|email', '%' . $keyword . '%');
        }
        $list = $this->paginate($model->where($map)->order('user_id desc'));
        if ($list) {
            $data = $list->toArray()['data'];
            foreach($data as $k=>$v){
                $data[$k]['avatar']=avatarUrl($v['avatar'],$v['realname'],$v['user_id'],120);
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }

    // 添加用户
    public function add()
    {
        try{
            $data = $this->request->param();
            $salt=\utils\Str::random(4);
            $data['password'] = md5($salt.$data['password'].$salt);
            $data['realname'] =$data['realname'];
            $data['email'] =$data['email'];
            $data['remark'] =$data['remark'];
            $data['salt'] =$salt;
            $data['name_py'] = pinyin_sentence($data['realname']);
            $data['status'] = 1;
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
        try{
            $data = $this->request->param();
            $user=UserModel::find($data['user_id']);
            if(!$user){
                return error('用户不存在');
            }
            $user->realname =$data['realname'];
            $user->email =$data['email'];
            $user->remark=$data['remark'];
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
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error('用户不存在');
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
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error('用户不存在');
        }
        try{
            $status = $this->request->param('status');
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
        return success('', $user);
    }

    // 设置用户角色
    public function setRole()
    {
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error('用户不存在');
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
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error('用户不存在');
        }
        try{
            $password = $this->request->param('password','');
            if($password){
                $salt=$user->salt;
                $user->password= md5($salt.$password.$salt);
            }
            $user->save();
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

    // 更换账户
    public function editAccount()
    {
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error('用户不存在');
        }
        try{
            $account = $this->request->param('account');
            $other=UserModel::where([['account','=',$account],['user_id','<>',$user_id]])->find();
            if($other){
                return error('账户已存在');
            }
            $user->account=$account;
            $user->save();
            return success('修改成功');
        }catch (\Exception $e){
            return error('修改失败');
        }
    }

}