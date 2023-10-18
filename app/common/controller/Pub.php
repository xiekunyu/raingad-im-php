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
        Gateway::$registerAddress = config('gateway.registerAddress');
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        // $this->initialize();
    }

    public function login(){
        $param=request()->param();
        $userInfo=User::where(['account'=> $param['account']])->withoutField('register_ip,login_count,update_time,create_time')->find();
        if($userInfo==null){
            return warning('当前用户不存在！');
        }elseif($userInfo['status']==0){
            return warning('您的账号已被禁用');
        }else{
            $password=password_hash_tp($param['password'],$userInfo['salt']);
            $code=$param['code'] ?? '';
            if($code){
                if($code!=Cache::get($param['account'])){
                    return warning('验证码错误！');
                }
                Cache::delete($param['account']);
            }else{
                if($password!=$userInfo['password']){
                    return warning('密码错误！');
                }
            }
            $userInfo['avatar']=avatarUrl($userInfo['avatar'],$userInfo['realname'],$userInfo['user_id']);
            //    如果用户已经有设置
            $setting=$userInfo['setting'] ?: '';
            if($setting){
                $setting['hideMessageName']= $setting['hideMessageName']=='true' ? true : false;
                $setting['hideMessageTime']= $setting['hideMessageTime']=='true' ? true : false;
                $setting['avatarCricle']= $setting['avatarCricle']=='true' ? true : false;
                $setting['isVoice']= $setting['isVoice']=='true' ? true : false;
                $setting['sendKey']=(int)$setting['sendKey'];
                $userInfo['setting']=$setting;
            }
            //如果登录信息中含有client——id则自动进行绑定
            $client_id=$this->request->param('client_id');
            if($client_id){
                $cid=$this->request->header('cid','');
                $this->doBindUid($userInfo['user_id'],$client_id,$cid);
            }
            $update=[
                'last_login_time'=>time(),
                'last_login_ip'=>$this->request->ip(),
                'login_count'=>Db::raw('login_count+1')
            ];
            User::where('user_id',$userInfo['user_id'])->update($update);
            $userInfo['qrUrl']=request()->domain().'/scan/u/'.encryptIds($userInfo['user_id']);
            unset($userInfo['password'],$userInfo['salt']);
            $userInfo['displayName']=$userInfo['realname'];
            $userInfo['id']=$userInfo['user_id'];
            $authToken=User::refreshToken($userInfo,$param['terminal'] ?? 'web');
            $data=[
                'sessionId'=>Session::getId(),
                'authToken'=>$authToken,
                'userInfo'=>$userInfo
            ];
            return success('登录成功！',$data);
       }
   }

    //退出登录
    public function logout(){
        try {
            $jwtData = JWTAuth::auth();
        } catch (\Exception $e) {
            return success('退出成功！');
        }

        $userInfo = $jwtData['info']->getValue();
        //解密token中的用户信息
        $userInfo = str_encipher($userInfo,false, config('app.aes_token_key'));

        if (!$userInfo) {
            return success('退出成功！');
        }
        //解析json
        $userInfo = (array)json_decode($userInfo, true);
        if($userInfo){
            $client_id=$this->request->param('client_id','');
            if($client_id){
                Gateway::unbindUid($client_id,$userInfo['user_id']);
            }
            wsSendMsg(0,'isOnline',['id'=>$userInfo['user_id'],'is_online'=>0]);
        }
        JWTAuth::invalidate(JWTAuth::token()->get());
        return success('退出成功！');
    }

    // 注册用户
    public function register(){
        try{
            $data = $this->request->param();
            $systemInfo=Config::getSystemInfo();
            // 判断系统是否开启注册
            if($systemInfo['sysInfo']['regtype']==2){
                $inviteCode=$data['inviteCode'] ?? '';
                if(!$inviteCode){
                    return warning('当前系统已关闭注册功能！');
                }
                if(!Cache::get($inviteCode)){
                    return warning('邀请码已失效！');
                }
            }
            $code=$data['code'] ?? '';
            if($code){
                if($code!=Cache::get($data['account'])){
                    return warning('验证码错误！');
                }
                Cache::delete($data['account']);
            }
            $user=new User();
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
            // 监听用户注册后的操作
            event('UserRegister',$data);
            return success('注册成功', $data);
        }catch (\Exception $e){
            return error($e->getMessage());
        }

    }

    //头像生成
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
        $cid=$this->request->param('cid','');
        try{
            $this->doBindUid($user_id,$client_id,$cid);
        }catch(\Exception $e){
            // 未找到用户
        }
        return success('');
    }

    // 执行绑定
    public function doBindUid($user_id,$client_id,$cid=''){
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
        if($cid){
            bindCid($user_id,$cid);
        }
        wsSendMsg(0,'isOnline',['id'=>$user_id,'is_online'=>1]);
    }

    // 下线通知
    public function offline(){
        $user_id=input('user_id');
        try{
            $client_ids=Gateway::getClientIdByUid($user_id);
            // 一个终端登录时才发送下线通知
            if(count($client_ids)<2){
                wsSendMsg(0,'isOnline',['id'=>$user_id,'is_online'=>0]);
            }
        }catch(\Exception $e){
            // 未找到用户
        }
        return success('');
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

    // 获取系统配置信息
    public function getSystemInfo(){
        $systemInfo=Config::getSystemInfo();
        $systemInfo['demon_mode']=env('app.demon_mode',false);
        return success('',$systemInfo);
    }

    // 发送验证码
    public function sendCode(){
        $account=$this->request->param('account');
        $type=$this->request->param('type',1);
        if(in_array($type,[3,4]) && !$account){
            $userInfo=request()->userInfo;
            $acType=\utils\Regular::check_account($userInfo['account']);
            if($acType){
                $account=$userInfo['account'];
            }else{
                $account=$userInfo['email'];
            }
        };
        $acType=\utils\Regular::check_account($account);
        if(!$acType){
            return warning('账户必须为手机号或者邮箱');
        }
        if(Cache::get($account.'_time')) return warning('请一分钟后再试！');
        if($type==1){
            $text='登录账户';
            $actions="login";
        }elseif($type==2){
            $text='注册账户';
            $actions="register";
        }elseif($type==3){
            $text='修改密码';
            $actions="changePassword";
        }else{
            $text="修改账户";
            $actions="changeUserinfo";
        }
        $code=rand(100000,999999);
        Cache::set($account,$code,300);
        Cache::set($account.'_time',$code,60);
        if($acType==2){
            $conf=Config::where(['name'=>'smtp'])->value('value');
            $conf['temp']='code';
            $mail=new \mail\Mail($conf);
            $mail->sendEmail([$account],$text,$code);
            return success('发送成功');
        }else{
            $parmes=[
                'code'=>$code
            ];
            $res=sendSms($account,$actions,$parmes);
            return success($res['msg']);
        }
    }
    
}
