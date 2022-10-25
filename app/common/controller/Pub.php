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
                    if($setting['hideMessageName']=='true'){
                        $setting['hideMessageName']=true;
                    }else{
                        $setting['hideMessageName']=false;
                    }
                    if($setting['hideMessageTime']=='true'){
                        $setting['hideMessageTime']=true;
                    }else{
                        $setting['hideMessageTime']=false;
                    }
                    if($setting['avatarCricle']=='true'){
                        $setting['avatarCricle']=true;
                    }else{
                        $setting['avatarCricle']=false;
                    }
                    if($setting['isVoice']=='true'){
                        $setting['isVoice']=true;
                    }else{
                        $setting['isVoice']=false;
                    }
                    $setting['sendKey']=(int)$setting['sendKey'];
                $userInfo['setting']=$setting;
               }
                //如果登录信息中含有client——id则自动进行绑定
               $client_id=$this->request->param('client_id');
               if($client_id){
                    $this->doBindUid($userInfo['user_id'],$client_id);
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
    $authToken=request()->header('authToken');
    $userInfo=[];
    if($authToken){
        $userInfo=Cache::get($authToken);
    }
    if($userInfo){
        wsSendMsg(0,'isOnline',['id'=>$userInfo['user_id'],'is_online'=>0]);
    }
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
        $client_id=$this->request->param('client_id');
        $user_id=$this->request->param('user_id');
        $this->doBindUid($user_id,$client_id);
        return success('');
    }

    // 执行绑定
    public function doBindUid($user_id,$client_id){
        // 如果当前ID在线，将其他地方登陆挤兑下线
        if(Gateway::isUidOnline($user_id)){
            wsSendMsg($user_id,'offline',['id'=>$user_id,'client_id'=>$client_id,'isMobile'=>$this->request->isMobile()]);
        }
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
        wsSendMsg(0,'isOnline',['id'=>$user_id,'is_online'=>1]);
    }

    // 下线通知
    public function offline(){
        $user_id=input('user_id');
        wsSendMsg(0,'isOnline',['id'=>$user_id,'is_online'=>0]);
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
