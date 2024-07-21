<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/14 17:24
 */


namespace app\manage\controller;


use app\BaseController;
use easyTask\Terminal;
use think\App;
use think\facade\Console;
use think\Response;

class Task extends BaseController
{
    /**
     * 项目根目录
     * @var string
     */
    protected $rootPath;

    protected $taskNames = [];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->rootPath = root_path();
        chdir($this->rootPath);
        $this->taskNames = [
            'schedule' => lang('task.schedule'),
            'queue' => lang('task.queue'),
            'worker' => lang('task.worker'),
            'clearStd' => lang('task.clearStd'),
        ];
    }

    /**
     * 任务列表
     * @return Response
     */
    public function getTaskList()
    {
        $data = $this->taskMsg();

        if (!count($data)) {
            return warning('');
        }

        foreach ($data as &$datum) {
            $expName = explode('_', $datum['name']);

            $datum['remark'] = $this->taskNames[$expName[count($expName) - 1]] ?? lang('task.null');
        }
        unset($datum);
        return success('', $data);
    }

    /**
     * 启动全部进程
     * @return Response
     */
    public function startTask()
    {
        if(strpos(strtolower(PHP_OS), 'win') === 0)
        {
            return warning(lang('task.winRun'));
        }

        if (count($this->taskMsg())) {
            return warning(lang('task.alreadyRun'));
        }

        // 启动
        $out = Terminal::instance(2)->exec('php think task start');
        if (!count($this->analysisMsg($out))) {
            return warning(lang('task.startFail'));
        }

        return success(lang('task.startOk'));
    }

    /**
     * 强制停止全部进程
     * @return Response
     */
    public function stopTask()
    {
        if (!count($this->taskMsg())) {
            return warning(lang('task.notRun'));
        }

        // 强制停止
        Terminal::instance(2)->exec('php think task stop force');

        return success('');
    }

    /**
     * 获取单个任务日志
     * @return Response
     */
    public function getTaskLog()
    {
        $name = $this->request->param('name');

        $path = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR . 'easy_task' . DIRECTORY_SEPARATOR . 'Std' . DIRECTORY_SEPARATOR;

        if (!file_exists($path . 'exec_' . $name . '.std')) {
            $expName = explode('_', $name);
            $name    = $expName[count($expName) - 1];
            if (!file_exists($path . 'exec_' . $name . '.std')) {
                return warning(lang('task.logExist'));
            }
        }

        return success('', file_get_contents($path . 'exec_' . $name . '.std'));
    }

    /**
     * 清理单个任务日志
     * @return Response
     */
    public function clearTaskLog()
    {
        $name = $this->request->param('name');

        $path = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR . 'easy_task' . DIRECTORY_SEPARATOR . 'Std' . DIRECTORY_SEPARATOR;

        if (!file_exists($path . 'exec_' . $name . '.std')) {
            $expName = explode('_', $name);
            $name    = $expName[count($expName) - 1];
            if (!file_exists($path . 'exec_' . $name . '.std')) {
                return warning(lang('task.logExist'));
            }
        }

        file_put_contents($path . 'exec_' . $name . '.std', '');
        return success('');
    }


    /**
     * 获取运行状态
     * @return array
     */
    private function taskMsg()
    {
        $out = Terminal::instance(2)->exec('php think task status');
        return $this->analysisMsg($out);
    }

    /**
     * 解析数据
     * @param string $out 带解析数据
     * @return array
     */
    private function analysisMsg(string $out)
    {
        $re = '/│ *([\w+]+) *│ *([\w+]+)[ ]*│ *([\w+]+|[0-9- :]+) *│ *([\w+]+) *│ *([\w+]+) *│ *([\w+]+) *│/m';

        preg_match_all($re, $out, $matches, PREG_SET_ORDER, 0);

        if (!count($matches)) {
            return [];
        }

        $data  = [];
        $names = $matches[0];
        unset($names[0]);
        $names = array_values($names);
        unset($matches[0]);

        foreach ($matches as $match) {
            $temp = [];
            foreach ($match as $key => $item) {
                if ($key !== 0) {
                    $temp[$names[$key - 1]] = $item;
                }
            }
            $data[] = $temp;
        }

        return $data;
    }
}