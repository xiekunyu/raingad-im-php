<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace app\worker;

use think\App;
use think\exception\Handle;
use think\exception\HttpException;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Response;
/**
 * Worker应用对象
 */
class Application extends App
{
    /**
     * 处理Worker请求
     * @access public
     * @param  \Workerman\Connection\TcpConnection   $connection
     * @param  void
     */
    public function worker(TcpConnection $connection)
    {
        try {
            $this->beginTime = microtime(true);
            $this->beginMem  = memory_get_usage();
            $this->db->clearQueryTimes();

            $pathinfo = ltrim(strpos($_SERVER['REQUEST_URI'], '?') ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'], '/');

            $this->request
                ->setPathinfo($pathinfo)
                ->withInput($GLOBALS['HTTP_RAW_POST_DATA']);

            while (ob_get_level() > 1) {
                ob_end_clean();
            }

            ob_start();
            $response = $this->http->run();
            $content  = ob_get_clean();

            ob_start();

            $response->send();
            $this->http->end($response);

            $content .= ob_get_clean() ?: '';

            $this->httpResponseCode($response->getCode());
            $header=[];
            foreach ($response->getHeader() as $name => $val) {
                // 发送头部信息
                $header[$name] =!is_null($val) ? $val : '';
            }
            if (strtolower($_SERVER['HTTP_CONNECTION']) === "keep-alive") {
                $connection->send(new Response(200, $header, $content));
            } else {
                $connection->close(new Response(200, $header, $content));
            }
        } catch (HttpException | \Exception | \Throwable $e) {
            $this->exception($connection, $e);
        }
    }

    /**
     * 是否运行在命令行下
     * @return bool
     */
    public function runningInConsole(): bool
    {
        return false;
    }

    protected function httpResponseCode($code = 200)
    {
            new Response($code);
    }

    protected function exception($connection, $e)
    {
        if ($e instanceof \Exception) {
            $handler = $this->make(Handle::class);
            $handler->report($e);

            $resp    = $handler->render($this->request, $e);
            $content = $resp->getContent();
            $code    = $resp->getCode();

            $this->httpResponseCode(new Response($code, [], $content));
            $connection->send($content);
        } else {
            $connection->send(new Response(500, [], $e->getMessage()));
        }
    }

}