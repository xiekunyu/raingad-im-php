<?php

namespace app\common\controller;

use think\App;
use app\enterprise\model\{User,Group};
use think\facade\Session;
use think\facade\Cache;
use GatewayClient\Gateway;

/**
 * 控制器基础类
 */
class Pub
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

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        // $this->initialize();
    }

   public function login(){
       $post=input('post.');
    //    if(!isset($post['account'])){
    //     $post['account']='admin';
    //     $post['password']='123456';
    //    }
       $userInfo=User::getUserInfo(['account'=>$post['username']]);
       if($userInfo==null){
            return error('当前用户不存在！');
       }elseif($userInfo['status']==0){
            return error('您的账号已被禁用');
       }else{
           $password=password_hash_tp($post['password'],$userInfo['salt']);
           if($password!=$userInfo['password']){
                return error('密码错误！');
           }else{
               $authToken=ssoTokenEncode($userInfo['user_id'],"raingadIm",300);
               $userInfo['avatar']=avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id']);
            //    如果用户已经有设置
               if($userInfo['setting']){
                    $setting=json_decode($userInfo['setting'],true);
                    $setting['hideMessageName']=false;
                    $setting['hideMessageTime']=false;
                    $setting['avatarCricle']=false;
                    $setting['isVoice']=false;
                    if($setting['hideMessageName']=='true'){
                        $setting['hideMessageName']=true;
                    }
                    if($setting['hideMessageTime']=='true'){
                        $setting['hideMessageTime']=true;
                    }
                    if($setting['avatarCricle']=='true'){
                        $setting['avatarCricle']=true;
                    }
                    if($setting['isVoice']=='true'){
                        $setting['isVoice']=true;
                    }
                    $setting['sendKey']=(int)$setting['sendKey'];
                $userInfo['setting']=$setting;
               }
               $data=[
                   'sessionId'=>Session::getId(),
                   'authToken'=>$authToken,
                   'userInfo'=>$userInfo
               ];
               Cache::set($authToken,$userInfo);
               return success('登录成功！',$data);
           }
       }
   }

//    退出登录
   public function logout(){
    // $authToken=$this->request->header('authToken');
    // echo $authToken;die;
    // if(Cache::get($authToken)){
    //     Cache::delete($authToken);
    // }
    return success('退出成功！');
   }


   public function register(){
       $salt="srww";
       if(!$this->postData){
           $data=[
               'account'=>'admin',
               'realname'=>"管理员",
               'password'=>md5('123456'.$salt)

           ];
       }else{
           $data=$this->postData;
       }
       $data['salt']=$salt;
       $data['create_time']=time();
       User::addData($data);
       return success('操作成功');

   }

//    头像生成
   public function avatar(){
    circleAvatar(input('str'),input('s')?:80,input('uid'));die;
   }

    /**
     * 将用户UId绑定到消息推送服务中
     * @return \think\response\Json
     */
    public function bindUid(){
        $client_id=input('client_id');
        $user_id=input('user_id');
        Gateway::bindUid($client_id, $user_id);
        // 查询团队，如果有团队则加入团队
        $group=Group::getMyGroup(['gu.user_id'=>$user_id,'gu.status'=>1]);
        if($group){
            $group=$group->toArray();
            $group_ids=arrayToString($group,'group_id',false);
            foreach($group_ids as $v){
                Gateway::joinGroup($client_id, $v); 
            }
        }
        return success();
        // pushMessage($this->userInfo['uid'],'notice','消息通知','恭喜您，已成功绑定UID','');
    }
  
 /**
     * 将用户团队绑定到消息推送服务中
     * @return \think\response\Json
     */
    public function bindGroup(){
        $client_id=input('client_id');
        $group_id=input('group_id');
        $group_id = explode('-', $group_id)[1];
        Gateway::joinGroup($client_id, $group_id); 
        return success();
    }
}
