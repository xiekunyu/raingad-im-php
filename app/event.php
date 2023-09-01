<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'UserRegister'=>['app\common\listener\UserRegister'],
        'GroupChange'=>['app\enterprise\listener\GroupChange'],
    ],

    'subscribe' => [
    ],
];
