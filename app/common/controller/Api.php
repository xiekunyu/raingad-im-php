<?php

namespace app\common\controller;

use think\App;
use app\enterprise\model\{User,Group};
use app\index\controller\Extension;
use think\facade\Session;
use think\facade\Cache;
use think\facade\Db;
use GatewayClient\Gateway;
use app\manage\model\Config;
use thans\jwt\facade\JWTAuth;


/**
 * API接口类
 */
class Api
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    protected $middleware=['apiAuth'];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
    }

    // 创建用户
    public function createUser()
    {
        $data = $this->request->param();
        if(!isset($data['account']) || !isset($data['realname'])){
            return warning('缺少参数');
        }
        $user=new User();
        $verify=$user->checkAccount($data);
        if(!$verify){
            return success('账号已存在');
        }
        $salt=\utils\Str::random(4);
        $data['password'] = password_hash_tp(rand(100000,999999),$salt);
        $data['salt'] =$salt;
        $data['register_ip'] =$this->request->ip();
        $data['name_py'] = pinyin_sentence($data['realname']);
        $user->save($data);
        $data['user_id']=$user->user_id;
        $data['open_id']=encryptIds($user->user_id);
        // 监听用户注册后的操作
        event('UserRegister',$data);
        return success('注册成功', $data);
    }

    // 用户登录
    public function login()
    {
        $param=$this->request->param();
        $isMobile=$param['is_mobile'] ?? false;
        if(!isset($param['account']) || !isset($param['open_id'])){
            return warning('缺少参数');
        }
        $userInfo=User::where(['account'=> $param['account']])->withoutField('register_ip,login_count,update_time,create_time')->find();
        if(!$userInfo){
            return warning('当前用户不存在！');
        }
        try{
            $hash_id=decryptIds($param['open_id']);
            if($hash_id!=$userInfo['user_id']){
                return warning('当前用户不存在！');
            }
        }catch (\Exception $e){
            return error($e->getMessage());
        }
        $md5=md5(json_encode($userInfo));
        // 将用户信息缓存5分钟
        Cache::set($md5,$userInfo,300);
        // 生成Url
        if($isMobile){
            $url=rtrim(request()->domain(),'/').'/h5/#/pages/login/index?token='.$md5;
        }else{
            $url=rtrim(request()->domain(),'/').'/#/login?token='.$md5;
        }
        return success('登录成功',$url);
        
    }
   
    
}
