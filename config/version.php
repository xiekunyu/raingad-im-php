<?php
# app_name 应用名称，所有的安装包都是用该名称命名，不能用中文！！！！！！！！！！！！

# VERSION：移动端app版本信息
# RELEASE：根据该参数确定版本，版本比移动罐的版本大就会提示更新，苹果和ios通过这个检测
# UPDATE_TYPE ：forcibly 强制更新, solicit弹窗确认更新, silent 静默更新 
# UPDATE_INFO ：更新说明，换行用\n 
return [
    'app_name'=>env('app.name', 'Raingad-IM'),  //在.env中配置
    'android' => [
        'version' => env('app.version', '6.0.0'),  //在.env中配置
        'release' => env('app.release', '20250520'), //在.env中配置
        'url' =>env('app.android_webclip',''),
        'update_info' => '1.新增国际化，优化大量UI\n2.新增新的保活插件\n3.聊天记录采用虚拟列表，提升性能\n4.修复若干BUG',
        'update_type' => 'solicit',
    ],
    'ios' => [
        'version' => env('app.version', '6.0.0'),  //在.env中配置
        'release' => env('app.release', '20250520'), //在.env中配置
        'url' => env('app.ios_webclip',''),
        'update_info' => '暂无',
        'update_type' => 'solicit',
    ],
    'windows' => [
        'version' => '6.0.0',
        'release' => '20250520',
        'url' => env('app.win_webclip',''),
        'update_info' => '1.lemon-imui本地化，消息底部检测',
        'update_type' => 'solicit',
    ],
    'mac' => [
        'version' => '4.0.0',
        'release' => '20240323',
        'url' => env('app.mac_webclip',''),
        'update_info' => '1.修复了一些bug\n2.优化了一些功能',
        'update_type' => 'solicit',
    ],
    'serve' => [
        'version' => '5.2.2',
        'release' => '20241118',
        'url' => '',
        'update_info' => '1.增加聊天记录查看\n2.增加系统公告，移动端首页滚动提醒\n3.增加群聊支持单个人禁言，支持新成员查看历史聊天记录\n4.优化移动端输入框，解决ios低版本H5问题\n5.修复若干BUG',
        'update_type' => 'solicit',
    ],
];