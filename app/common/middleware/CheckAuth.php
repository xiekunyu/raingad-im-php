<?php
namespace app\common\middleware;

use Exception;
use thans\jwt\exception\TokenInvalidException;
use thans\jwt\facade\JWTAuth;
//验证权限
class CheckAuth
{
    public function handle($request, \Closure $next)
    {
        try {
            $jwtData = JWTAuth::auth();
        } catch (Exception $exception) {

            //token有误
            if (get_class($exception) == TokenInvalidException::class) {
                return shutdown('登陆信息有误 请重新登录', -1);
            }

            $errorMsgArr = [
                'Must have token' => '请先登陆系统',
                'The token is in blacklist.' => '登陆已失效 请重新登陆',
                'The token is expired.' => '登陆已过期 请重新登陆',
                'The token is in blacklist grace period list.' => '登陆已过期 请重新登陆'
            ];
            return shutdown($errorMsgArr[$exception->getMessage()] ?? $exception->getMessage(), -1);
        }

        $userInfo = $jwtData['info']->getValue();
        //解密token中的用户信息
        $userInfo = str_encipher($userInfo,false, config('app.aes_token_key'));

        if (!$userInfo) {
            return shutdown('用户信息有误，请重新登陆', -1);
        }
        //解析json
        $userInfo = (array)json_decode($userInfo, true);
        //已经登陆，将用户信息存入请求头
        $request->userInfo  = $userInfo;
        $request->uid       = $userInfo['id'];
        $request->userToken = JWTAuth::token()->get();
        return $next($request);
    }
}