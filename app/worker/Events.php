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

 namespace app\worker;
/**
 * 推送主逻辑
 * 主要是处理 onMessage onClose 
 */
use GatewayWorker\Lib\Gateway;
use app\worker\Application;
use think\facade\Config;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use thans\jwt\provider\JWT\Lcobucci;
use utils\Aes;

class Events
{
    // 使用TP框架
    public static function onWorkerStart()
    {
        $app = new Application;
        $app->initialize();
    }

    // 当有客户端连接时，将client_id返回，让mvc框架判断当前uid并执行绑定
    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode(array(
            'type'      => 'init',
            'client_id' => $client_id
        )));
    }
    /**
    * 有消息时
    * @param int $client_id
    * @param mixed $message
    */
   public static function onMessage($client_id, $message)
   {
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }
        
        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            // 客户端回应服务端的心跳
            case 'pong':
                break;
            case 'ping':
                self::sendStatus($client_id);
                break;
            case 'bindUid':
                self::auth($client_id,$message_data);
                break;
        }
        return;
   }

   protected static function sendStatus($client_id){
        $uid=$_SESSION['user_id'] ?? 0;
        $multiport=false;
        if($uid){
            $arr=Gateway::getClientIdByUid($uid);
            if(count($arr)>1){
                $multiport=true;
            }
        }
        Gateway::sendToClient($client_id, json_encode(array(
            'type' => 'pong',
            'multiport' => $multiport,
        )));
   }

    //验证用户的真实性并绑定
    protected static function auth($client_id, $msg){
        $token=$msg['token'] ?? '';
        $config   = Config::get('jwt');
        $keys     = $config['secret'] ?: [
            'public' => $config['public_key'],
            'private' => $config['private_key'],
            'password' => $config['password'],
        ];
        $provider = new Lcobucci(new Builder(), new Parser(), $config['algo'], $keys);
        try {
            $token=str_replace('bearer ','',$token);
            $jwtData = $provider->decode((string)$token);
        } catch (\Exception $exception) {
            self::closeClient($client_id);
        }

        $userInfo = $jwtData['info']->getValue();
        //解密token中的用户信息
        $userInfo = Aes::decrypt($userInfo, config('app.aes_token_key'));
        //解析json
        $userInfo = (array)json_decode($userInfo, true);
        if(!$userInfo){
            self::closeClient($client_id);
        }
        $_SESSION['user_id']=$userInfo['user_id'];
        self::sendStatus($client_id);
    }

    //断开连接
    protected static function closeClient($client_id){
        $_SESSION['user_id']=null;
        Gateway::closeClient($client_id);
    }

    /**
    * 当断开连接时
    * @param int $client_id
    */
    public static function onClose($client_id)
    {
        $user_id=$_SESSION['user_id'];
        if($user_id){
            Gateway::sendToAll(json_encode(array(
            'type'      => 'isOnline',
            'time' => time(),
            'data' => ['id'=>$user_id,'is_online'=>0]
        )));
        }
        
    }
  
}
