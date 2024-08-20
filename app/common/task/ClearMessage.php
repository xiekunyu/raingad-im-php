<?php

namespace app\common\task;

use yunwuxin\cron\Task;
use think\Exception;
use app\manage\model\{Config};
use app\enterprise\model\Message;

// 自动清理消息定时任务
class ClearMessage extends Task
{
    
    //    定时任务日志内容
    protected $content='';
    protected $path='';
    protected $daytime=86400;

    /**
     * 自动写入定时任务日志
     * @return \think\response\Json
     */
    protected function writeLog($text)
    {
        $this->path = root_path() . 'crontab.txt';

        $content = '重置中！';
        if (!file_exists($this->path)) {
            fopen($this->path, 'w');
        }
        if (date('d') != 10) {
            $content = file_get_contents($this->path);
        }
        file_put_contents($this->path, $content . date('Y-m-d H:i:s') . '：' . $text . PHP_EOL);
    }

    public function configure()
    {
        //设置每天2点执行
        $this->dailyAt('02:00'); 
    }

    /**
     * 执行任务
     * @return mixed
     */
    protected function execute()
    {
        if(date('H:i')!='02:00'){
            return false;
        }
        try {
           $config=Config::getSystemInfo();
           $status=$config['chatInfo']['msgClear'] ?? false;
           $days=$config['chatInfo']['msgClearDay'] ?? 0;
           if($status && $days){
                $time=time() - ($days * $this->daytime);
                $where[]=['create_time','<',$time];
                Message::where($where)->delete();
           }
           print "****************消息清理成功******************\n";
        } catch (Exception $e) {
            print '消息清理失败:'.$e->getMessage()."\n";
        }
    }
}
