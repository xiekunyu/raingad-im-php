<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \GatewayWorker\BusinessWorker;
use \GatewayWorker\Gateway;
use \GatewayWorker\Register;
use think\App;
use think\facade\Config;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Events.php';

$app=new App;
$app->initialize();

// bussinessWorker 进程

$worker = new BusinessWorker();
// worker名称
$worker->name = 'PushBusinessWorker';
// bussinessWorker进程数量
$worker->count = 1;
// 服务注册地址
$worker->registerAddress = config('gateway.registerAddress');
$worker->eventHandler = 'Events';


// gateway 进程

$gateway = new Gateway(config('gateway.protocol')."://".config('gateway.host').':'.config('gateway.port'));
// 设置名称，方便status时查看
$gateway->name = config('gateway.name');
// 设置进程数，gateway进程数建议与cpu核数相同
$gateway->count = 1;
// 分布式部署时请设置成内网ip（非127.0.0.1）
$gateway->lanIp = config('gateway.lanIp');
// 内部通讯起始端口。假如$gateway->count=4，起始端口为2300
// 则一般会使用2300 2301 2302 2303 4个端口作为内部通讯端口 
$gateway->startPort = config('gateway.startPort');
// 心跳间隔
$gateway->pingInterval = config('gateway.pingInterval');
// 心跳数据
$gateway->pingData = config('gateway.pingData');
// 服务注册地址
$gateway->registerAddress = config('gateway.registerAddress');


// register 服务必须是text协议

$register = new Register('text://0.0.0.0:1237');

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

