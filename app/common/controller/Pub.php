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

use function Hyperf\Coroutine\wait;

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
        $token=$param['token'] ?? '';
        // token一键登录
        if($token){
            $apiStatus=config('app.api_status');
            if(!$apiStatus){
                return warning(lang('system.apiClose'));
            }
            $userInfo=Cache::get($token);
            if(!$userInfo){
                return warning(lang('user.tokenFailure'));
            }
        }else{
            $verifyTime=md5(request()->ip());
            $hasError=Cache::get($verifyTime);
            if($hasError && $hasError>5){
                return warning(lang('user.loginLimit'));
            }
            $userInfo=User::where(['account'=> $param['account']])->withoutField('register_ip,login_count,update_time,create_time')->find();
            if($userInfo==null){
                return warning(lang('user.exist'));
            }
            if($userInfo['status']==0){
                return warning(lang('user.forbid'));
            }
            $password=password_hash_tp($param['password'],$userInfo['salt']);
            $code=$param['code'] ?? '';
            if($code){
                if($code!=Cache::get($param['account'])){
                    return warning(lang('user.codeErr'));
                }
                Cache::delete($param['account']);
            }else{
                if($password!=$userInfo['password']){
                    $hasError++;
                    Cache::set($verifyTime,$hasError,300);
                    return warning(lang('user.passError'));
                }
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
        $userInfo['qrUrl']=getMainHost().'/scan/u/'.encryptIds($userInfo['user_id']);
        unset($userInfo['password'],$userInfo['salt']);
        $userInfo['displayName']=$userInfo['realname'];
        $userInfo['id']=$userInfo['user_id'];
        $authToken=User::refreshToken($userInfo,$param['terminal'] ?? 'web');
        $data=[
            'sessionId'=>Session::getId(),
            'authToken'=>$authToken,
            'userInfo'=>$userInfo
        ];
        return success(lang('user.loginOk'),$data);
   }

    //退出登录
    public function logout(){
        try {
            $jwtData = JWTAuth::auth();
        } catch (\Exception $e) {
            return success(lang('user.logoutOk'));
        }

        $userInfo = $jwtData['info']->getValue();
        //解密token中的用户信息
        $userInfo = str_encipher($userInfo,false, config('app.aes_token_key'));

        if (!$userInfo) {
            return success(lang('user.logoutOk'));
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
        return success(lang('user.logoutOk'));
    }

    // 注册用户
    public function register(){
        if(env('app.demon_mode',false)){
            return warning(lang('system.demoMode'));
        }
        try{
            $data = $this->request->param();
            $ip = $this->request->ip();
            $systemInfo=Config::getSystemInfo();
            $registerInterval=$systemInfo['sysInfo']['registerInterval'] ? : 0;
            if(Cache::has('register_'.md5($ip)) && $registerInterval>0){
                return warning(lang('user.registerLimit',['time'=>floor($registerInterval/60)]));
            }
            // 判断系统是否开启注册
            if($systemInfo['sysInfo']['regtype']==2){
                $inviteCode=$data['inviteCode'] ?? '';
                if(!$inviteCode){
                    return warning(lang('user.closeRegister'));
                }
                if(!Cache::get($inviteCode)){
                    return warning(lang('user.inviteCode'));
                }
            }
            $code=$data['code'] ?? '';
            if($code){
                if($code!=Cache::get($data['account'])){
                    return warning(lang('user.codeErr'));
                }
                Cache::delete($data['account']);
            }
            // 接入用户名检测服务
            event('GreenText',['content'=>$data['realname'],'service'=>"nickname_detection"]);
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
            // x分钟后才能再注册
            if($registerInterval){
                Cache::set('register_'.md5($ip),$ip,$registerInterval);
            }
            return success(lang('user.registerOk'), $data);
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
        return success('');
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
            return warning(lang('user.accountVerify'));
        }
        if(Cache::get($account.'_time')) return warning(lang('user.waitMinute'));
        if($type==1){
            $text=lang('user.loginAccount');
            $actions="login";
        }elseif($type==2){
            $text=lang('user.registerAccount');
            $actions="register";
        }elseif($type==3){
            $text=lang('user.editPass');
            $actions="changePassword";
        }else{
            $text=lang('user.editAccount');
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
            return success(lang('system.sendOk'));
        }else{
            $parmes=[
                'code'=>$code
            ];
            $res=sendSms($account,$actions,$parmes);
            return success($res['msg']);
        }
    }

    // 检查app版本升级
    public function checkVersion(){
        $oldRelease=$this->request->param('release',0);
        $setupPage=$this->request->param('setupPage',false);
        $platform=$this->request->param('platform',1101);
        $name=config('version.app_name');
        $packageName='';
        if($platform==1101){
            $teminal='andriod';
        }else{
            $teminal='ios';
        }
        $versionInfo=config('version.'.$teminal);
        $data=[
            'versionName'=>$versionInfo['version'],
            'versionCode'=>$versionInfo['release'],
            'updateType'=>$versionInfo['update_type'],
            'versionInfo'=>$versionInfo['update_info'],
            'downloadUrl'=>'',
        ];
        // 是否手动检测更新，是的话就不能强制更新或者静默更新
        if($setupPage){
            $data['updateType']='solicit';
        }
        // 如果旧版本大于等于当前版本则不更新
        if($oldRelease>=$versionInfo['release']){
            return success('',$data);
        }
        $downUrl='';
        $andriod='';
        // 如果是ios则返回ios地址
        if($platform==1101){
            $packageName=$name."_Setup_".$versionInfo['version'].".apk";
            if(is_file(PACKAGE_PATH . $packageName)){
                $andriod = getMainHost().'/unpackage/'.$packageName;
            }
            $downUrl=env('app.andriod_webclip','') ? : $andriod;
        }else{
            $downUrl=env('app.ios_webclip','');
        }
        $data['downloadUrl']=$downUrl;
        return success('',$data);
    }
    
}
