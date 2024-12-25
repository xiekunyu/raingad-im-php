<?php
namespace app\index\controller;

use app\enterprise\model\{File,Group,User};
use think\facade\View;
use app\manage\model\Config;
use app\Request;

class Index
{

    public function index()
    {
        if (!file_exists(PACKAGE_PATH . "install.lock")) {
            return redirect(url('index/install/index'));
        }
        // 自动跳转后无法注册
        // if(request()->isMobile() && !env('app.demon_mode',false)){
        //     return redirect("/h5");
        // }
        return redirect("/index.html");
    }

    public function view()
    {
        $url=request()->param('src');
        $suffix=explode('.',$url);
        $ext=$suffix[count($suffix)-1];
        return View::fetch('',[
            'url'  => $url,
            'ext'=>$ext,
            'name'=>lang('file.preview')
        ]);
    }

    //    头像生成
    public function avatar()
    {
        circleAvatar(input('str'), input('s') ?: 80, input('uid'));die;
    }

    // 文件下载
    public function download()
    {
        
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            throw new \think\Exception(lang('file.browserDown'),400);
        }
        $param = request()->param();
        $file_id = $param['file_id'] ?? 0;
        if (!$file_id) {
            throw new \think\Exception(lang('system.parameterError'), 502);
        }
        try {
            $file_id = decryptIds($file_id);
        } catch (\Exception $e) {
            throw new \think\Exception($e->getMessage(), 400);
        }
        $file = File::find($file_id);
        if (!$file) {
            throw new \think\Exception(lang('file.exist'),404);
        }
        $file = $file->toArray();
        // 兼容本地文件下载
        $fileUrl=getDiskUrl();
        if($fileUrl==getMainHost()){
            $url=rtrim(public_path(),'/').$file['src'];
        }else{
            $url= getFileUrl($file['src']);
        }
        return \utils\File::download($url, $file['name'] . '.' . $file['ext'], $file['size'], $file['ext']);
    }

    // 扫码获取信息
    public function scanQr(){
        $param=request()->param();
        $action=$param['action'] ?? '';
        $token=$param['token'] ?? '';
        $realToken=$param['realToken'] ?? '';
        if(request()->isPost() && $action && $token && $realToken){
            $actions=[
                'g'=>'group',
                'u'=>'user',
            ];
            $a=$actions[$action] ?? '';
            if(!$a){
                return warning(lang('scan.failure'));
            }
            return $this->$a($param);
        }else{
            return $this->index();
        }
    }

    protected function group($param)
    {
        $token=authcode(urldecode($param['realToken']),"DECODE", 'qr');
        if(!$token){
            return warning(lang('scan.failure'));
        }
        $groupInfo=explode('-',$token);
        $uid=$groupInfo[0];
        $group_id=$groupInfo[1];
        $group=Group::find($group_id);
        if($group){
            $group=$group->toArray();
            $group['avatar']=avatarUrl($group['avatar'],$group['name'],$group_id,120);
            $group['invite_id']=$uid;
            $group['id']='group-'.$group_id;
            $group['action']='groupInfo';
            return success('',$group);
        }else{
            return warning(lang('scan.failure'));
        }
    }

    protected function user($param)
    {
        $id=decryptIds($param['token']);
        if(!$id){
            return warning(lang('scan.failure'));
        }
        $user=User::where(['user_id'=>$id])->field(User::$defaultField)->find();
        if($user){
            $user=$user->toArray();
            $user['avatar']=avatarUrl($user['avatar'],$user['realname'],$user['user_id'],120);
            $user['id']=$user['user_id'];
            $user['action']='userInfo';
            return success('',$user);
        }else{
            return warning(lang('scan.failure'));
        }
    }

    // app下载页
    public function downApp(){
        // echo request()->domain(true);
        $downAppUrl=env('app.downApp_url','');
        if($downAppUrl){
            return redirect($downAppUrl);
        }
        $config=Config::where('name','sysInfo')->value('value');
        $andriod=getAppDowmUrl('andriod');
        $winUrl=getAppDowmUrl('windows');
        $macUrl=getAppDowmUrl('mac');
        $client=[
            'andriod_appid'=>env('app.andriod_appid',''),
            'andriod_webclip'=>env('app.andriod_webclip','') ? : $andriod,
            'ios_appid'=>env('app.ios_appid',''),
            'ios_webclip'=>env('app.ios_webclip',''),
            'win_webclip'=>env('app.win_webclip','') ? : $winUrl,
            'mac_webclip'=>env('app.mac_webclip','') ? : $macUrl
        ];
        $noUrl=false;
        if(!$client['andriod_appid'] && !$client['andriod_webclip']  && !$client['ios_appid'] && !$client['ios_webclip']){
           $noUrl=true;
        }
        View::assign('noUrl',$noUrl);
        View::assign('client',$client);
        View::assign('config',$config);
        return View::fetch();
    }

    // 下载APP
    public function downloadApp(){
        
        $platform=request()->param('platform','windows');
        $config=config('version.'.$platform);
        $name=config('version.app_name');
        if($platform=='andriod'){
            $packageName=$name."_Setup_".$config['version'].".apk";
        }elseif($platform=='mac'){
            $packageName=$name."_Setup_".$config['version'].".dmg";
        }else{
            $packageName=$name."_Setup_".$config['version'].".exe";
        }
        $file=PACKAGE_PATH . $packageName;
        if(is_file($file)){
            return \utils\File::download($file, $packageName);
        }else{
            return shutdown(lang('file.exist'));
        }
    }
}
