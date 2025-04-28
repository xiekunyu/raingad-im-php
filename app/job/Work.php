<?php
/**
 * User raingad
 * Date 2021/8/2 15:31
 */

namespace app\job;

use app\common\controller\Upload;

use app\enterprise\model\{Group,GroupUser,User,Message,File};

use Exception;

use think\queue\Job;

use think\facade\Filesystem;

class Work
{
    // 允许的队列
    protected $actions=[
        'createAvatar', //创建、更新头像
        'clearFiles', //清理文件
        'clearVoice', //清理音频
        'setIsRead'
    ];

    // 执行队列
    public function fire(Job $job, $data)
    {
        $action = $data['action'] ?? '';
        //如果重试超过3次，或者没有action或者没有type，直接删除队列
        if ($job->attempts() > 3 || !in_array($action,$this->actions)) {
            $job->delete();
            return;
        }
        try {
            // 根据不同的方法执行处理数据
            $res=$this->$action($data);
            if($res){
                print("<info>".$action." is success</info> \n");
            }else{
                print("<info>".$action." is error</info> \n");
            }
        } catch (\Exception $e) {
            print("<info>".$action." error: ".$e->getMessage()."</info> \n");
            
        }
        $job->delete();
    }

    // 创建头像
    public function createAvatar($data)
    {
        $group_id=$data['group_id'] ?? 0;
        if(!$group_id){ return false;}
        $userList = GroupUser::where('group_id', $group_id)->limit(9)->column('user_id');
        $userList = User::where('user_id', 'in', $userList)->select()->toArray();
        $imgList  = [];
        $dirPath  = app()->getRootPath() . 'public/temp';
        foreach ($userList as $k => $v) {
            if ($v['avatar']) {
                $imgList[] = avatarUrl($v['avatar'], $v['realname'], $v['user_id']);
            } else {
                $imgList[] = circleAvatar($v['realname'], 80, $v['user_id'], 1, $dirPath);
            }
        }
        $groupId = 'group_' . $group_id;
        $path    = $dirPath . '/' . $groupId . '.jpg';
        $a       = getGroupAvatar($imgList, 1, $path);
        $url     = '';
        if ($a) {
            $upload  = new Upload();
            $newPath = $upload->uploadLocalAvatar($path, [], $groupId);
            if ($newPath) {
                Group::where('group_id', $group_id)->update(['avatar' => $newPath]);
                $url = avatarUrl($newPath);
            }
        }
        $files = glob($dirPath . '/*'); // 获取目录下所有文件路径
        foreach ($files as $file) {
            if (is_file($file)) { // 如果是文件则删除
                unlink($file);
            }
        }
        wsSendMsg($group_id,"setManager",['group_id'=>'group-'.$group_id,'avatar'=>$url],1);
        return true;
    }

    // 清理除了音频相关的文件
    public function clearFiles($data){
        $fileIds = $data['fileIds'];
        $count=0;
        foreach($fileIds as $fileId){
            $message=Message::where(['file_id'=>$fileId])->count();
            // 如果还有消息，就不删除文件
            if($message>0){
                continue;
            }
            $file=File::find($fileId);
            if($file){
                $MD5=$file->md5;
                $src=$file->src;
                $file->delete();
                // 查询相同文件
                $sameFile=File::where(['md5'=>$MD5])->count();
                // 如果有相同的文件，则不删除原件
                if($sameFile){
                    continue;
                }
                $count++;
                // 删除源文件
                $disk=env('filesystem.driver','local');
                Filesystem::disk($disk)->delete($src);
            }
            print("<info>成功删除".$count."个文件！</info> \n");
            return true;
        }
    }

    // 清理除了音频相关的文件
    public function clearVoice($data){
        $list = $data['list'];
        foreach($list as $content){
            $src = str_encipher($content,false);
            // 解密文件路径，删除源文件
            $disk=env('filesystem.driver','local');
            Filesystem::disk($disk)->delete($src);
        }
        print("<info>成功删除".count($list)."个音频文件！</info> \n");
        return true;
    }

    // 设置已读
    public function setIsRead($data){
        $is_group=$data['is_group'];
        $to_user= $data['to_user'];
        $messages=$data['messages'];
        $user_id=$data['user_id'];
        if ($is_group==1) {
            $toContactId = explode('-', $to_user)[1];
            // 将@消息放到定时任务中逐步清理
            if($messages){
                Message::setAtRead($messages,$user_id);
            }
            // 更新群里面我的所有未读消息为0
            GroupUser::editGroupUser(['user_id' => $user_id, 'group_id' => $toContactId], ['unread' => 0]);
        } else if($is_group==0) {
            $chat_identify = chat_identify($user_id, $to_user);
            // 更新我的未读消息为0
            Message::update(['is_read' => 1], [['chat_identify', '=', $chat_identify], ['to_user', '=', $user_id]]);
            // 告诉对方我阅读了消息
            wsSendMsg($to_user, 'readAll', ['toContactId' => $user_id]);
        } 
        return true;
    }
    

}
