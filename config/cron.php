<?php

return [
    'tasks' => [
        \app\common\task\ClearMessage::class, //定时清理消息
        \app\common\task\SetAtRead::class, //定时清理@消息
        ]
];