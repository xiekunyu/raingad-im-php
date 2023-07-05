<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'local'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root'       => app()->getRootPath() . 'public/storage',
        ],
        // 更多的磁盘配置信息
        'aliyun' => [
            'type'         => 'aliyun',
            'accessId'     => env('filesystem.aliyun_accessId',''),
            'accessSecret' => env('filesystem.aliyun_accessSecret',''),
            'bucket'       => env('filesystem.aliyun_bucket',''),
            'endpoint'     => env('filesystem.aliyun_endpoint',''),
            'url'          => env('filesystem.aliyun_url',''),//不要斜杠结尾，此处为URL地址域名。
        ],
        'qiniu'  => [
            'type'      => 'qiniu',
            'accessKey' => env('filesystem.qiniu_accessKey',''),
            'secretKey' => env('filesystem.qiniu_secretKey',''),
            'bucket'    => env('filesystem.qiniu_bucket',''),
            'url'       => env('filesystem.qiniu_url',''),//不要斜杠结尾，此处为URL地址域名。
        ],
        'qcloud' => [
            'type'       => 'qcloud',
            'region'      => env('filesystem.qcloud_region',''),//bucket 所属区域 英文
            'appId'      => env('filesystem.qcloud_appId',''), // 域名中数字部分
            'secretId'   => env('filesystem.qcloud_secretId',''),
            'secretKey'  => env('filesystem.qcloud_secretKey',''),
            'bucket'          => env('filesystem.qcloud_bucket',''),
            'timeout'         => 60,
            'connect_timeout' => 60,
            'cdn'             => env('filesystem.qcloud_cdn',''),
            'scheme'          => 'https',
            'read_from_cdn'   => false,
        ]
    ],
];
