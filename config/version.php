<?php
# app_name 应用名称，所有的安装包都是用该名称命名，不能用中文！！！！！！！！！！！！

# VERSION：移动端app版本信息
# RELEASE：根据该参数确定版本，版本比移动罐的版本大就会提示更新，苹果和ios通过这个检测
# UPDATE_TYPE ：forcibly 强制更新, solicit弹窗确认更新, silent 静默更新 
# UPDATE_INFO ：更新说明，换行用\n 
return [
    'app_name'=>'Raingad-IM',
    'andriod' => [
        'version' => '4.1.4',
        'release' => '20240428',
        'url' =>env('app.andriod_webclip',''),
        'update_info' => '1.增加探索页面的演示\n2.修复了一些bug',
        'update_type' => 'solicit',
    ],
    'ios' => [
        'version' => '4.1.0',
        'release' => '20240323',
        'url' => env('app.ios_webclip',''),
        'update_info' => '暂无',
        'update_type' => 'solicit',
    ],
    'windows' => [
        'version' => '4.1.3',
        'release' => '20240328',
        'url' => env('app.win_webclip',''),
        'update_info' => '1.增加自动更新机制',
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
        'version' => '4.1.5',
<<<<<<< HEAD
        'release' => '20240615',
=======
        'release' => '20240531',
>>>>>>> a5eb08d2594a01cc977ccd9d199338dde112cff7
        'url' => '',
        'update_info' => '1.增加群公告消息通知\n2.优化了一些功能',
        'update_type' => 'solicit',
    ],
];