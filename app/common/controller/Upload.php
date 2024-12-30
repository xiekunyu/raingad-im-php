<?php
/**
 * lvzheAdmin [a web admin based ThinkPHP5]
 * @author    xiekunyu<raingad@foxmail.com>
 */
namespace app\common\controller;

use app\BaseController;
use app\enterprise\model\{File as FileModel,Message,User,Emoji}; 
use app\manage\model\{Config}; 
use think\facade\Filesystem;
use think\facade\Request;
use think\File;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate\TimeCode;

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
        $uid=request()->userInfo['user_id'] ?? 1;
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
            return shutdown(lang('file.uploadLimit',['size'=>$conf['size']]));
        }
        // 兼容uniapp文件上传
        if($info['ext']=='' && isset($data['ext'])){
            $info['ext']=$data['ext'];
        }
        $info['ext']=strtolower($info['ext']);
        if(!in_array($info['ext'],$conf['fileExt'])){
            return shutdown(lang('file.typeNotSupport'));
        }
        $fileType=getFileType($info['ext']);
        $imageInfo=[];
        if($fileType==2){
            $filecate="image";
            $imageInfo=$this->getImageSizeInfo($info['path']);
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
            $prefix=$filecate.'/'.date('Y-m-d').'/'.$uid."/";
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
            'videoInfo'=>$imageInfo
        ];
        
        if($message){
            // 自动获取视频第一帧,视频并且是使用的阿里云
            if($message['type']=='video'){
                $videoInfo=$this->getVideoCover($filePath);
                if($videoInfo){
                    $extends=$videoInfo['videoInfo'];
                    $extends['poster']=$this->url.$videoInfo['src'];
                    $message['extends']=$extends;
                }else{
                    $message['extends']['poster']=getMainHost().'/static/common/img/video.png';
                }
                // if($this->disk=='aliyun'){
                //     $message['extends']['poster']=$this->url.$ret['src'].'?x-oss-process=video/snapshot,t_1000,m_fast,w_800,f_png';
                // }else{
                //     $message['extends']['poster']=getMainHost().'/static/common/img/video.png';
                // }
            }
            // 如果发送的文件是图片、视频、音频则将消息类型改为对应的类型
            if(in_array($fileType,[2,3,4])){
                $message['type']=$filecate;
            }
            if($message['type']=='image'){
                $message['extends']=$imageInfo;
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
            $messageModel=new Message();
            $data=$messageModel->sendMessage($message,$this->globalConfig);
            if(!$data){
                return shutdown($messageModel->getError());
            }
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
            return success(lang('file.uploadOk'),$info);
        } catch(\Exception $e) {
            return error($e->getMessage().$e->getLine());
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
            return success(lang('file.uploadOk'),$url);
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
            return success(lang('file.uploadOk'),$url);
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

    // 上传表情
    public function uploadEmoji(){
        $param=request::param();
        try{
            $file=request()->file('file');
            $filePath = $file;
            $uid=request()->userInfo['user_id'] ?? 1;
            $info=$this->getFileInfo($filePath,$file,true);
            if($info['ext']==''){
                $pathInfo        = pathinfo($message['fileName'] ?? '');
                $info['ext']     = $pathInfo['extension'];
                $info['name']    =$message['fileName'] ?? '';
            }
            // 表情不能大于1m
            if(2*1024*1024 < $info['size']){
                return shutdown(lang('file.uploadLimit',['size'=>2]));
            }
            // 兼容uniapp文件上传
            if($info['ext']=='' && isset($param['ext'])){
                $info['ext']=$param['ext'];
            }
            $info['ext']=strtolower($info['ext']);
            if(!in_array($info['ext'],['jpg','jpeg','gif','png'])){
                return shutdown(lang('file.typeNotSupport'));
            }
            $prefix='emoji/'.$uid.'/';
            $name=str_replace('.'.$info['ext'],'',$info['name']);
            $fileInfo=FileModel::where(['md5'=>$info['md5']])->find();
            // 判断文件是否存在，如果有则不再上传
            if(!$fileInfo){
                $newName   = uniqid() . '.' . $info['ext'];
                $object = $prefix . $newName;
                if($this->disk=='local'){
                    $object='storage/'.$object;
                }
                Filesystem::disk($this->disk)->putFileAs($prefix, $filePath, $newName);
                $ret = [
                    "src"      => $object,
                    "name"     => $name,
                    "cate" => 1,
                    "size"     => $info['size'],
                    "md5"     => $info['md5'],
                    "file_type"     => $info['mime'],
                    "ext"     => $info['ext'],
                    "type"     =>2,
                    'user_id'=>$uid,
                ];
                $fileInfo=new FileModel;
                $fileInfo->save($ret);
            }else{
                $object = $fileInfo->src;
            }
            // 把左边的/去掉再加上，避免有些有/有些没有
            $object='/'.ltrim($object,'/');
            $emojiInfo=[
                'user_id'  => $uid,
                "src"      => $object,
                "name"     => $name,
                "type"     => 2,
                "file_id"  => $fileInfo->file_id,
            ];
            Emoji::create($emojiInfo);
            return success('',$this->url.$object);
        } catch(\Exception $e) {
            return $e->getMessage().$e->getLine();
        }
    }

    // 获取图片的尺寸
    protected function getImageSizeInfo($file){
        $extends=[];
        // 如果图片获取图片的尺寸
        $imageSize = getimagesize($file);
        $extends['width']=$imageSize[0];
        $extends['height']=$imageSize[1];
        // 如果宽大于高则为横图，宽度填充模式，否则为竖图，高度填充模式
        if($imageSize[0]>=$imageSize[1]){
            $extends['fixMode']=1;  // 宽度填充
        }else{
            $extends['fixMode']=2;  // 高度填充
        }
        if($imageSize[0]<200 && $imageSize[1]<240){
            $extends['fixMode']=3;    // 小图
        }
        return $extends;
    }

    // 获取视频封面
    public function getVideoCover($filePath){
        $fileName=pathinfo($filePath,PATHINFO_FILENAME).'.jpg';
        $ffmpegPath=env('ffmpeg.bin_path','');
        if(!$ffmpegPath){
            return false;
        }
        $path=array(
            'ffmpeg.binaries'  => $ffmpegPath.'/ffmpeg',
            'ffprobe.binaries' => $ffmpegPath.'/ffprobe',
            'timeout'          => 3600, // 进程超时时间
            'ffmpeg.threads'   => 12,   // FFMpeg应使用的线程数
        );
        $ffmpeg = FFMpeg::create($path);
        $ffprobe = FFProbe::create($path);
        $duration=$ffprobe->format($filePath)->get('duration');// 获取 duration 属性
        $video = $ffmpeg->open($filePath);
        $frame = $video->frame(TimeCode::fromSeconds(1));
        $tempPath=root_path().'public/temp';
        $savePath=$tempPath. '/' .$fileName;
        $frame->save($savePath);
        $info=$this->upload([],$savePath,'cover/'.date('Y-m-d').'/',false);
        $info['videoInfo']['duration']= ceil($duration);
        unlink($savePath);
        return $info;
    }

}
