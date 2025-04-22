<?php
/**
 * User raingad
 * Date 2021/8/2 15:31
 */

namespace app\job;

use app\common\controller\Upload;

use app\enterprise\model\{Group,GroupUser,User};

use Exception;

use think\queue\Job;

class Work
{
    // 允许的队列
    protected $actions=[
        'createAvatar', //创建、更新头像
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

}
