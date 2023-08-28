<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'queue:work' => think\queue\command\Work::class,
        'queue:listen' => think\queue\command\Listen::class,
        'queue:Restart' => think\queue\command\Restart::class,
        'task' => task\command\Task::class,
        'worker:gateway' => app\worker\command\GatewayWorker::class
    ],
];
