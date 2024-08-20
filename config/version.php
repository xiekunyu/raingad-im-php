<?php
# app_name 应用名称，所有的安装包都是用该名称命名，不能用中文！！！！！！！！！！！！

# VERSION：移动端app版本信息
# RELEASE：根据该参数确定版本，版本比移动罐的版本大就会提示更新，苹果和ios通过这个检测
# UPDATE_TYPE ：forcibly 强制更新, solicit弹窗确认更新, silent 静默更新 
# UPDATE_INFO ：更新说明，换行用\n 
return [
    'app_name'=>'Raingad-IM',
    'andriod' => [
        'version' => '5.1.0',
        'release' => '20240820',
        'url' =>env('app.andriod_webclip',''),
        'update_info' => '1.提示音本地化\n2.增加头像预览\n3.后台增加了链接自动识别\n4.修复若干BUG',
        'update_type' => 'solicit',
    ],
    'ios' => [
        'version' => '4.2.0',
        'release' => '20240628',
        'url' => env('app.ios_webclip',''),
        'update_info' => '暂无',
        'update_type' => 'solicit',
    ],
    'windows' => [
        'version' => '5.0.1',
        'release' => '20240727',
        'url' => env('app.win_webclip',''),
        'update_info' => '1.增加自动客服和自动群聊\n2.增加注册间隔和消息频率\n3.增加探索页配置\n4.修复若干BUG',
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
        'version' => '5.1.0',
        'release' => '20240820',
        'url' => '',
        'update_info' => '1.增加自动客服和自动群聊\n2.增加注册间隔和消息频率\n3.增加探索页配置\n4.修复若干BUG',
        'update_type' => 'solicit',
    ],
];