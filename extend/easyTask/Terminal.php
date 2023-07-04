<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/15 11:03
 */

namespace easyTask;

class Terminal
{
    /**
     * @var object 对象实例
     */
    protected static $instance;

    protected $rootPath;

    /**
     * 命令执行输出文件
     */
    protected $outputFile = null;

    /**
     * proc_open 的参数
     */
    protected $descriptorsPec = [];


    protected $pipes = null;

    protected $procStatus = null;
    protected $runType    = 1;


    /**
     * @param int $runType 1 task使用 输出连续记录 2 普通使用 输出读取后删除
     * @return object|static
     */
    public static function instance($runType, $outputName = null)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($runType, $outputName);
        }
        return self::$instance;
    }

    public function __construct($runType, $outputName = null)
    {
        $this->rootPath = root_path();
        $this->runType  = $runType;

        // 初始化日志文件

        if ($this->runType === 1) {
            $outputDir = Helper::getStdPath();

            $this->outputFile = $outputDir . 'exec_' . $outputName . '.std';
        } else {
            $outputDir = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR;

            $this->outputFile = $outputDir . 'exec_' . getOnlyToken() . '.log';
            file_put_contents($this->outputFile, '');
        }


        // 命令执行结果输出到文件而不是管道
        $this->descriptorsPec = [0 => ['pipe', 'r'], 1 => ['file', $this->outputFile, 'a'], 2 => ['file', $this->outputFile, 'a']];
    }

    public function __destruct()
    {
        // 类销毁 删除文件,type为2才删除
        if ($this->runType == 2) {
            unlink($this->outputFile);
        }
    }

    public function exec(string $command)
    {

        $this->process = proc_open($command, $this->descriptorsPec, $this->pipes, $this->rootPath);

        foreach ($this->pipes as $pipe) {
            fclose($pipe);
        }

        proc_close($this->process);

        if ($this->runType == 2) {
            $contents = file_get_contents($this->outputFile);
            return $contents;
        }
    }

    public function getProcStatus(): bool
    {
        $status = proc_get_status($this->process);
        return (bool)$status['running'];
    }


}