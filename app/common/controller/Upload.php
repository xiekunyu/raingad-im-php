<?php
/**
 * lvzheAdmin [a web admin based ThinkPHP5]
 * @author    xiekunyu<raingad@foxmail.com>
 */
namespace app\common\controller;

use app\BaseController;
use app\enterprise\model\{File as FileModel,Message,User}; 
use app\manage\model\{Config}; 
use think\facade\Filesystem;
use think\facade\Request;
use think\File;
class Upload extends BaseController
{
    protected $middleware = ['checkAuth'];
    protected $disk='';
    protected $url='';

    public function __construct()
    {
        parent::__construct(app());
        $this->disk=env('filesystem.driver','local');
        $this->url=getDiskUrl().'/';
        
    }

    /**
     * 文件上传
     */
    public function upload($data,$path,$prefix = "",$fileObj = true)
    {
        $message=$data['message'] ?? '';
        if($message){
            $message=json_decode($message,true);
        }
        $uid=request()->userInfo['user_id'];
        if($fileObj){
            $filePath = $path;
        }else{
            $filePath = new File($path);
        }
        $info=$this->getFileInfo($filePath,$path,$fileObj);
        if($info['ext']=='' && $message){
            $pathInfo        = pathinfo($message['fileName'] ?? '');
            $info['ext']     = $pathInfo['extension'];
            $info['name']    =$message['fileName'] ?? '';
        }
        $conf=Config::where(['name'=>'fileUpload'])->value('value');
        if($conf['size']*1024*1024 < $info['size']){
            return shutdown('文件大小超过限制');
        }
        // 兼容uniapp文件上传
        if($info['ext']=='' && isset($data['ext'])){
            $info['ext']=$data['ext'];
        }
        if(!in_array($info['ext'],$conf['fileExt'])){
            return shutdown('文件格式不支持');
        }
        $fileType=getFileType($info['ext']);
        if($fileType==2){
            $filecate="image";
        }elseif($fileType==3){
            $msgType=$message['type'] ?? '';
            // 如果是语音消息，类型才为语音，否者为文件，主要是兼容发送音频文件
            if($msgType=='voice'){
                $filecate="voice";
            }else{
                $filecate="file";
            }
        }elseif($fileType==4){
            $filecate="video";
        }else{
            $filecate="file";
        }
        if(!$prefix){
            $prefix=$filecate.'/'.$uid.'/'.date('Y-m-d')."/";
        }
        $name=str_replace('.'.$info['ext'],'',$info['name']);
        $file=FileModel::where(['md5'=>$info['md5']])->find();
        // 判断文件是否存在，如果有则不再上传
        if(!$file){
            $newName   = uniqid() . '.' . $info['ext'];
            $object = $prefix . $newName;
            if($this->disk=='local'){
                $object='storage/'.$object;
            }
            Filesystem::disk($this->disk)->putFileAs($prefix, $filePath, $newName);
        }else{
            $object = $file['src'];
        }
        // 把左边的/去掉再加上，避免有些有/有些没有
        $object='/'.ltrim($object,'/');
        $ret = [
            "src"      => $object,
            "name"     => $name,
            "cate" => $fileType,
            "size"     => $info['size'],
            "md5"     => $info['md5'],
            "file_type"     => $info['mime'],
            "ext"     => $info['ext'],
            "type"     =>2,
            'user_id'=>$uid,
        ];
        
        if($message){
            // 自动获取视频第一帧,视频并且是使用的阿里云
            if($message['type']=='video' && $this->disk=='aliyun'){
                $message['extends']['poster']=$this->url.$ret['src'].'?x-oss-process=video/snapshot,t_1000,m_fast,w_800,f_png';
            }else{
                $message['extends']['poster']='https://im.file.raingad.com/static/image/video.png';
            }
            // 如果发送的文件是图片、视频、音频则将消息类型改为对应的类型
            if(in_array($fileType,[2,3,4])){
                $message['type']=$filecate;
            }
            $newFile=new FileModel;
            // 录音就不保存了
            if($message['type']!='voice'){
                $newFile->save($ret);
            }
            $message['content']=$ret['src'];
            $message['file_id']=$newFile->file_id ?? 0;
            $message['file_cate']=$fileType;
            $message['file_size']=$info['size'];
            $message['file_name']= $name.'.'.$info['ext'];
            $message['user_id']= $uid;
            $data=Message::sendMessage($message);
            return $data;
        }else{
            return $ret;
        }
        
    }

    // 上传一般文件
    public function uploadFile(){
        $param=$this->request->param();
        try{
            $file=request()->file('file');
            $info=$this->upload($param,$file);
            return success("上传成功",$info);
        } catch(\Exception $e) {
            return error($e->getMessage());
        }
    }


    // 获取上传文件的信息
    protected function getFileInfo($file,$path,$isObj=false){
        $info= [
            'path'=>$file->getRealPath(),
            'size'=>$file->getSize(),
            'mime'=>$file->getMime(),
            'ext'=>$file->extension(),
            'md5'=>$file->md5(),
        ];
        if($isObj){
            $info['name']=$file->getOriginalName();
        }else{
            // 根据路径获取文件名
            $pathInfo        = pathinfo($path);
            $info['name']    = $pathInfo['basename'];
        }
        return $info;
        
    }

    // 上传图片
    public function uploadImage(){
        $param=request::param();
        try{
            $file=request()->file('file');
            $info=$this->upload($param,$file,'image/'.date('Y-m-d').'/');
            $url=$this->url.$info['src'];
            return success("上传成功",$url);
        } catch(\Exception $e) {
            return error($e->getMessage());
        }
    }

    // 普通上传头像
    public function uploadAvatar(){
        $param=request::param();
        try{
            $file=request()->file('file');
            $uid=request()->userInfo['user_id'];
            $info=$this->upload($param,$file,'avatar/'.$uid.'/');
            User::where(['user_id'=>$uid])->update(['avatar'=>$info['src']]);
            $url=$this->url.$info['src'];
            return success("上传成功",$url);
        } catch(\Exception $e) {
            return error($e->getMessage());
        }
    }

    // 服务器上传头像
    public function uploadLocalAvatar($file,$param,$uid){
        try{
            $info=$this->upload($param,$file,'avatar/'.$uid.'/',false);
            return $info['src'];
        } catch(\Exception $e) {
            return $e->getMessage().$e->getLine();
        }
    }


}
