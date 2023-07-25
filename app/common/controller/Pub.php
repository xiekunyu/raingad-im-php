<?php

namespace app\common\controller;

use think\App;
use app\enterprise\model\{User,Group};
use think\facade\Session;
use think\facade\Cache;
use think\facade\Db;
use GatewayClient\Gateway;
use app\manage\model\Config;

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
       $userInfo=User::getUserInfo(['account'=>$post['account']]);
       if($userInfo==null){
            return warning('当前用户不存在！');
       }elseif($userInfo['status']==0){
            return warning('您的账号已被禁用');
       }else{
           $password=password_hash_tp($post['password'],$userInfo['salt']);
           $code=$post['code'] ?? '';
           if($code){
                if($code!=Cache::get($post['account'])){
                    return warning('验证码错误！');
                }
                Cache::delete($post['account']);
           }else{
                if($password!=$userInfo['password']){
                    return warning('密码错误！');
                }
           }
           $authToken=ssoTokenEncode($userInfo['user_id'],"raingadIm",300);
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
                $this->doBindUid($userInfo['user_id'],$client_id);
            }
            $update=[
                'last_login_time'=>time(),
                'last_login_ip'=>$this->request->ip(),
                'login_count'=>Db::raw('login_count+1')
            ];
            User::where('user_id',$userInfo['user_id'])->update($update);
            $data=[
                'sessionId'=>Session::getId(),
                'authToken'=>$authToken,
                'userInfo'=>$userInfo
            ];
            Cache::set($authToken,$userInfo);
            return success('登录成功！',$data);
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
            $user=User::where('account',$data['account'])->find();
            if($user){
                return warning('账户已存在');
            }
            $code=$data['code'] ?? '';
            if($code){
                if($code!=Cache::get($data['account'])){
                    return warning('验证码错误！');
                }
                Cache::delete($data['account']);
            }else{
                return warning('验证码不能为空！');
            }
            // 验证账号是否为手机号或者邮箱
            if(!\utils\Regular::is_email($data['account']) && !\utils\Regular::is_phonenumber($data['account'])){
                return warning('账户必须为手机号或者邮箱');
            }
            $salt=\utils\Str::random(4);
            $data['password'] = password_hash_tp($data['password'],$salt);
            $data['salt'] =$salt;
            $data['name_py'] = pinyin_sentence($data['realname']);
            $user=new User();
            $user->save($data);
            $data['user_id']=$user->user_id;
            return success('添加成功', $data);
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
        if(Cache::get($account.'_time')) return warning('请一分钟后再试！');
        if(!$account){
            return warning('请输入账户');
        }
        $type=$this->request->param('type',1);
        if(!\utils\Regular::is_email($account)){
            return warning('暂时仅支持邮箱验证码');
        }
        if($type==1){
            $text='登录账户';
        }elseif($type==2){
            $text='注册账户';
        }elseif($type==3){
            $text='修改密码';
        }else{
            $text="修改账户";
        }
        $code=rand(100000,999999);
        Cache::set($account,$code,300);
        Cache::set($account.'_time',$code,60);
        $conf=Config::where(['name'=>'smtp'])->value('value');
        $conf['temp']='code';
        $mail=new \mail\Mail($conf);
        $mail->sendEmail([$account],$text,$code);
        return success('发送成功');
    }
    
}
