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

    public function test(){
        $img = array(
            circleAvatar('熊大',120,2,1),
            'https://im.file.raingad.com/logo/logo.png',
            'https://im.file.raingad.com/logo/logo.png',
            'https://im.file.raingad.com/logo/logo.png',
            'https://im.file.raingad.com/logo/logo.png'
        );
        $a = $this->getGroupAvatar($img,1,'./img/123.jpg');
        var_dump($a);
    }

/**
 * 合成图片
 * @param  array   $pic_list  [图片列表数组]
 * @param  boolean $is_save   [是否保存，true保存，false输出到浏览器]
 * @param  string  $save_path [保存路径]
 * @return boolean|string
 */
function getGroupAvatar($pic_list=array(),$is_save=false,$save_path=''){
    //验证参数
    if(empty($pic_list) || empty($save_path)){
        return false;
    }
    if($is_save){
        //如果需要保存，需要传保存地址
        if(empty($save_path)){
            return false;
        }
    }
    // 只操作前9个图片
    $pic_list = array_slice($pic_list, 0, 9);
    //设置背景图片宽高
    $bg_w = 150; // 背景图片宽度
    $bg_h = 150; // 背景图片高度
    //新建一个真彩色图像作为背景
    $background = imagecreatetruecolor($bg_w,$bg_h);
    //为真彩色画布创建白灰色背景，再设置为透明
    $color = imagecolorallocate($background, 202, 201, 201);
    imagefill($background, 0, 0, $color);
    imageColorTransparent($background, $color);
    //根据图片个数设置图片位置
    $pic_count = count($pic_list);
    $lineArr = array();//需要换行的位置
    $space_x = 3;
    $space_y = 3;
    $line_x = 0;
    switch($pic_count) {
        case 1: // 正中间
            $start_x = intval($bg_w/4); // 开始位置X
            $start_y = intval($bg_h/4); // 开始位置Y
            $pic_w = intval($bg_w/2); // 宽度
            $pic_h = intval($bg_h/2); // 高度
            break;
        case 2: // 中间位置并排
            $start_x = 2;
            $start_y = intval($bg_h/4) + 3;
            $pic_w = intval($bg_w/2) - 5;
            $pic_h = intval($bg_h/2) - 5;
            $space_x = 5;
            break;
        case 3:
            $start_x = 40; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/2) - 5; // 宽度
            $pic_h = intval($bg_h/2) - 5; // 高度
            $lineArr = array(2);
            $line_x = 4;
            break;
        case 4:
            $start_x = 4; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/2) - 5; // 宽度
            $pic_h = intval($bg_h/2) - 5; // 高度
            $lineArr = array(3);
            $line_x = 4;
            break;
        case 5:
            $start_x = 30; // 开始位置X
            $start_y = 30; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(3);
            $line_x = 5;
            break;
        case 6:
            $start_x = 5; // 开始位置X
            $start_y = 30; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(4);
            $line_x = 5;
            break;
        case 7:
            $start_x = 53; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(2,5);
            $line_x = 5;
            break;
        case 8:
            $start_x = 30; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(3,6);
            $line_x = 5;
            break;
        case 9:
            $start_x = 5; // 开始位置X
            $start_y = 5; // 开始位置Y
            $pic_w = intval($bg_w/3) - 5; // 宽度
            $pic_h = intval($bg_h/3) - 5; // 高度
            $lineArr = array(4,7);
            $line_x = 5;
            break;
    }
    foreach( $pic_list as $k=>$pic_path ) {
        $kk = $k + 1;
        if ( in_array($kk, $lineArr) ) {
            $start_x = $line_x;
            $start_y = $start_y + $pic_h + $space_y;
        }
        //获取图片文件扩展类型和mime类型，判断是否是正常图片文件
        //非正常图片文件，相应位置空着，跳过处理
        $image_mime_info = @getimagesize($pic_path);
        if($image_mime_info && !empty($image_mime_info['mime'])){
            $mime_arr = explode('/',$image_mime_info['mime']);
            if(is_array($mime_arr) && $mime_arr[0] == 'image' && !empty($mime_arr[1])){
                switch($mime_arr[1]) {
                    case 'jpg':
                    case 'jpeg':
                        $imagecreatefromjpeg = 'imagecreatefromjpeg';
                        break;
                    case 'png':
                        $imagecreatefromjpeg = 'imagecreatefrompng';
                        break;
                    case 'gif':
                    default:
                        $imagecreatefromjpeg = 'imagecreatefromstring';
                        $pic_path = file_get_contents($pic_path);
                        break;
                }
                //创建一个新图像
                $resource = $imagecreatefromjpeg($pic_path);
                //将图像中的一块矩形区域拷贝到另一个背景图像中
                // $start_x,$start_y 放置在背景中的起始位置
                // 0,0 裁剪的源头像的起点位置
                // $pic_w,$pic_h copy后的高度和宽度
                imagecopyresized($background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource));
            }
        }
        // 最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度
        $start_x = $start_x + $pic_w + $space_x;
    }
    if($is_save){
        $dir = pathinfo($save_path,PATHINFO_DIRNAME);
        if(!is_dir($dir)){
            $file_create_res = mkdir($dir,0777,true);
            if(!$file_create_res){
                return false;//没有创建成功
            }
        }
        $res = imagejpeg($background,$save_path);
        imagedestroy($background);
        if($res){
            return true;
        }else{
            return false;
        }
    }else{
        //直接输出
        header("Content-type: image/jpg");
        imagejpeg($background);
        imagedestroy($background);
    }
}
    
    
}
