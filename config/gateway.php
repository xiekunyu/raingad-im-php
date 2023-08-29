<?php
return [
    // 扩展自身需要的配置
    'protocol'              => 'websocket', // 协议 支持 tcp udp unix http websocket text
    'host'                  => '0.0.0.0', // 监听地址
    'port'                  => env('worker_port',8282), // 监听端口
    'socket'                => '', // 完整监听地址
    'context'               => [], // socket 上下文选项
    'register_deploy'       => env('worker_register_deploy',true), // 是否需要部署register
    'businessWorker_deploy' => true, // 是否需要部署businessWorker
    'gateway_deploy'        => true, // 是否需要部署gateway

    // Register配置
    'registerAddress'       => env('worker_register_address','127.0.0.1:1236'),

    // Gateway配置
    'name'                  => env('worker_name','pushGateWay'),
    'count'                 => env('worker_count',1),
    'lanIp'                 => env('worker_lan_ip','127.0.0.1'),
    'startPort'             => env('worker_start_port',2300),
    'daemonize'             => false,
    'pingInterval'          => 20,
    'pingNotResponseLimit'  => 0,
    'pingData'              => '{"type":"ping"}',

    // BusinsessWorker配置
    'businessWorker'        => [
        'name'         => 'BusinessWorker',
        'count'        => 1,
        'eventHandler' => 'app\worker\Events',
    ],

];