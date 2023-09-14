<?php
namespace app\index\controller;

use app\enterprise\model\{File,Group,User};
use think\facade\View;

class Index
{

    public function index()
    {
        if (!file_exists(PUBLIC_PATH . "install.lock")) {
            return redirect(url('index/install/index'));
        }
        return redirect("/index.html");
    }

    public function view()
    {
        return view::fetch();
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
            throw new \think\Exception('请使用浏览器下载!',400);
        }
        $param = request()->param();
        $file_id = $param['file_id'] ?? 0;
        if (!$file_id) {
            throw new \think\Exception('参数错误', 502);
        }
        try {
            $file_id = decryptIds($file_id);
        } catch (\Exception $e) {
            throw new \think\Exception($e->getMessage(), 400);
        }
        $file = File::find($file_id);
        if (!$file) {
            throw new \think\Exception('该文件不存在!',404);
        }
        $file = $file->toArray();
        // 兼容本地文件下载
        $fileUrl=getDiskUrl();
        if($fileUrl==request()->domain()){
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
                return warning('二维码已失效');
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
            return warning('二维码已失效');
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
            return warning('二维码已失效');
        }
    }

    protected function user($param)
    {
        $id=decryptIds($param['token']);
        if(!$id){
            return warning('二维码已失效');
        }
        $user=User::where(['user_id'=>$id])->field(User::$defaultField)->find();
        if($user){
            $user=$user->toArray();
            $user['avatar']=avatarUrl($user['avatar'],$user['realname'],$user['user_id'],120);
            $user['id']=$user['user_id'];
            $user['action']='userInfo';
            return success('',$user);
        }else{
            return warning('二维码已失效');
        }
    }
}
