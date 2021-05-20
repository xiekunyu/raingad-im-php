<?php
/**
 * lvzheAdmin [a web admin based ThinkPHP5]
 * @author    xiekunyu<raingad@foxmail.com>
 */
namespace app\controller;

use app\BaseController;
use app\model\{File,Message};
use OSS\OssClient;
use think\facade\Db;
use OSS\Core\OssException;
class Upload extends BaseController
{
//公共配置文件
    protected $config=[
        'bucket'=>"raingad-im-file",
        'src'=>"https://im.file.raingad.com/"
    ];

        protected $mainHost="https://im.raingad.com";
    /**
     * 文件上传
     */
    public function upload($data,$oss,$prefix = "")
    {
        $uid=$this->userInfo['user_id'];
        $filePath =request()->file('file');
        $info=$this->getFileInfo($filePath);
        $fileType=getFileType($info['ext']);
        if($fileType==2){
            $filecate="image";
        }else{
            $filecate="file";
        }
        if(!$prefix){
            $prefix=$filecate.'/'.$uid.'/'.date('Y-m-d')."/";
        }
        $name=str_replace('.'.$info['ext'],'',$info['name']);
        $file=File::where(['md5'=>$info['md5']])->find();
        // 判断文件是否存在，如果有则不再上传
        if(!$file){
            // 如果没有开启oss文件服务，默认存本地
            if($oss['accessKeyId']==""){
                $savename = \think\facade\Filesystem::disk('public')->putFile( $filecate, $filePath,uniqid().'.'.$info['ext']);
                $object='storage/'.$savename;
            }else{
                $object = $prefix.uniqid().'.'.$info['ext'];
                $ossClient = new OssClient($oss['accessKeyId'], $oss['accessKeySecret'],$oss['endpoint']);
                $ossClient->uploadFile($oss['bucket'], $object, $info['path']);
            }
        }else{
            $object = $file['src'];
        }
        
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
        $message=json_decode($data['message'],true);
        $message['content']=$ret['src'];
        $message['file_size']=$info['size'];
        $message['file_name']= $name.'.'.$info['ext'];
        $message['user_id']= $uid;
        $data=Message::sendMessage($message);
        $newFile=new File;
        $newFile->save($ret);
        return $data;
    }

    // 上传一般文件
    public function uploadFile(){
        $param=$this->request->param();
        try{
            $oss=config('oss');
            $info=$this->upload($param,$oss);
            return success("上传成功",$info);
        } catch(OssException $e) {
            return error($e->getMessage());
        }
    }


    // 获取上传文件的信息
    protected function getFileInfo($file){
        return [
            'name'=>$file->getOriginalName(),
            'path'=>$file->getRealPath(),
            'size'=>$file->getSize(),
            'mime'=>$file->getMime(),
            'ext'=>$file->extension(),
            'md5'=>$file->md5(),
        ];
    }

    //    删除文档库文件
    public function delFile(){
        $fileId=decrypt(input('hash_id'));
        $info=File::find($fileId);
        try {
            if ($info) {
                $count=Db::name('file')->where(['md5'=>$info['md5']])->count();
                if($count==1){
                    $oss =config('oss');
                    $ossClient = new OssClient($oss['accessKeyId'], $oss['accessKeySecret'], $oss['endpoint']);
                    if (!$ossClient->doesObjectExist($this->cloudBucket['bucket'], $info['src'])) {
                        return warning('该文件不存在');
                    }
                    $ossClient->deleteObject($this->cloudBucket['bucket'], $info['src']);
                }
                File::destroy($fileId);
                return success('删除成功');
            } else {
                return warning("文件不能存在");
            }
        }catch (\Exception $e){
            return error(502,$e->getMessage());
        }

    }
}
