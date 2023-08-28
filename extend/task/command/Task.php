<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/14 16:12
 */


namespace task\command;


use easyTask\Helper;
use easyTask\Task as EasyTask;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\helper\Str;

class Task extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('task')
             ->addArgument('action', Argument::OPTIONAL, "action", '')
             ->addArgument('force', Argument::OPTIONAL, "force", '')
             ->setDescription('the task command');
    }

    protected function execute(Input $input, Output $output)
    {
        //获取输入参数
        $action = trim($input->getArgument('action'));
        $force  = trim($input->getArgument('force'));

        $rootPath = root_path();

        // 配置任务，每隔20秒访问2次网站
        $task = new EasyTask();

        // 设置常驻内存
        $task->setDaemon(!Helper::isWin());

        // 设置项目名称 获取运行目录文件夹名称
        $task->setPrefix('easy_task');

        // 设置子进程挂掉自动重启
        $task->setAutoRecover(true);

        // 设置运行时目录(日志或缓存目录)
        $task->setRunTimePath($rootPath . 'runtime');
        // 消息推送
        $task->addCommand('php think worker:gateway start', 'worker', 0);
        // 定时任务
        $task->addCommand('php think cron:schedule', 'schedule', 0);
        // 律者队列
        $task->addCommand('php think queue:listen --sleep 0.3 --queue lvzhe', 'queue', 0);
        

        // 根据命令执行
        if ($action == 'start') {
            $task->start();
        } elseif ($action == 'status') {
            $task->status();
        } elseif ($action == 'stop') {
            $force = ($force == 'force'); //是否强制停止
            $task->stop($force);
        } else {
            exit('Command is not exist');
        }
    }

}