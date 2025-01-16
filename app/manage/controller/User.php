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
use think\facade\Cache;

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
                $data[$k]['location']=$v['last_login_ip'] ? implode(" ", \Ip::find($v['last_login_ip'])) : '--';
                $data[$k]['reg_location']=$v['register_ip'] ? implode(" ", \Ip::find($v['register_ip'])) : '--';
                $data[$k]['last_login_time']=$v['last_login_time'] ? date('Y-m-d H:i:s',$v['last_login_time']) : '--';
                unset($data[$k]['password']);
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }

    // 添加用户
    public function add()
    {
        try{
            $data = $this->request->param();
            $user=new UserModel();
            $verify=$user->checkAccount($data);
            if(!$verify){
                return warning($user->getError());
            }
            $salt=\utils\Str::random(4);
            $data['password'] = password_hash_tp($data['password'],$salt);
            $data['salt'] =$salt;
            $data['register_ip'] =$this->request->ip();
            $data['name_py'] = pinyin_sentence($data['realname']);
            $user->save($data);
            $data['user_id']=$user->user_id;
            return success(lang('system.addOk'), $data);
        }catch (\Exception $e){
            return error(lang('system.addFail'));
        }
    }

    // 修改用户    
    public function edit()
    {
        try{
            $data = $this->request->param();
            $user=new UserModel();
            $verify=$user->checkAccount($data);
            if(!$verify){
                return warning($user->getError());
            }
            $user=UserModel::find($data['user_id']);
            $user->account =$data['account'];
            $user->realname =$data['realname'];
            $user->email =$data['email'];
            $user->remark=$data['remark'];
            $user->sex =$data['sex'] ?? 0;
            $user->friend_limit =$data['friend_limit'];
            $user->group_limit =$data['group_limit'];
            $csUid=$data['cs_uid'] ?? 0;
            if($csUid && $csUid==$data['user_id']){
                return warning(lang('user.notOwn'));
            }
            $user->cs_uid =$data['cs_uid'];
            // 只有超管才能设置管理员
            if($this->userInfo['user_id']==1){
                $user->role =$data['role'];
            }
            $user->status =$data['status'];
            $user->name_py= pinyin_sentence($data['realname']);
            $user->save();
            return success(lang('system.editOk'), $data);
        }catch (\Exception $e){
            return error(lang('system.editFail'));
        }
    }

    // 删除用户
    public function del()
    {
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user || $user->user_id==1){
            return warning(lang('user.exist'));
        }
        Db::startTrans();
        try{
            // 删除其好友关系
            Friend::where('create_user', $user_id)->whereOr(['friend_user_id'=>$user_id])->delete();
            // 删除其群组关系
            GroupUser::where('user_id', $user_id)->delete();
            UserModel::destroy($user_id);
            Db::commit();
            return success(lang('system.delOk'));
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
            return warning(lang('user.exist'));
        }
        try{
            $status = $this->request->param('status',0);
            // 将禁用状态写入缓存
            if(!$status){
                Cache::set('forbidUser_'.$user_id,true,env('jwt.ttl',86400));
            }
            UserModel::where('user_id', $user_id)->update(['status'=>$status]);
            return success(lang('system.editOk'));
        }catch (\Exception $e){
            return error(lang('system.editFail'));
        }
    }

    // 获取用户信息
    public function detail()
    {
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return error(lang('user.exist'));
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
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return warning(lang('user.exist'));
        }
        try{
            $role = $this->request->param('role');
            UserModel::where('user_id', $user_id)->update(['role'=>$role]);
            return success('');
        }catch (\Exception $e){
            return error('');
        }
    }

    // 修改密码
    public function editPassword()
    {
        $user_id = $this->request->param('user_id');
        $user=UserModel::find($user_id);
        if(!$user){
            return warning(lang('user.exist'));
        }
        try{
            $password = $this->request->param('password','');
            if($password){
                $salt=$user->salt;
                $user->password= password_hash_tp($password,$salt);
                Cache::set('forbidUser_'.$user_id,true,env('jwt.ttl',86400));
            }
            $user->save();
            return success('');
        }catch (\Exception $e){
            return error('');
        }
    }

}