<?php

namespace app\common\task;

use yunwuxin\cron\Task;
use think\Exception;
use think\facade\Cache;
use app\manage\model\{Config};
use app\enterprise\model\Message;

// 自动清理消息定时任务
class SetAtRead extends Task
{
    
    //    定时任务日志内容
    protected $content='';
    protected $path='';
    protected $daytime=86400;

    public function configure()
    {
        //设置每天8点执行
        $this->everyMinute(); 
    }

    /**
     * 执行任务
     * @return mixed
     */
    protected function execute()
    {
        try {
           $atListQueue=Cache::get('atListQueue');
            if($atListQueue){
                foreach ($atListQueue as $key=>$val){
                    $message=Message::where('msg_id',$key)->value('at');
                    $atList=($message ?? null) ? explode(',',$message): [];
                    // 两个数组取差集
                    $uniqueArr=array_unique($val);
                    $newAtList = array_filter($atList, function ($value) use ($uniqueArr) {
                        return !in_array($value, $uniqueArr);
                    });
                    Message::where('msg_id',$key)->update(['at'=>implode(',',$newAtList)]);
                }
                Cache::delete('atListQueue');
            }
            print "****************设置已读成功******************\n";
        } catch (Exception $e) {
            print '设置已读失败:'.$e->getMessage()."\n";
        }
    }
}
