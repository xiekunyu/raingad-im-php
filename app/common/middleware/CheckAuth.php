<?php
namespace app\common\middleware;

use Exception;
use thans\jwt\exception\TokenInvalidException;
use thans\jwt\facade\JWTAuth;
use think\facade\Cache;
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
                return shutdown(lang('user.loginError'), -1);
            }

            $errorMsgArr = [
                'Must have token' => lang('user.mustToken'),
                'The token is in blacklist.' => lang('user.blacklist'),
                'The token is expired.' => lang('user.expired'),
                'The token is in blacklist grace period list.' => lang('user.expired')
            ];
            return shutdown($errorMsgArr[$exception->getMessage()] ?? $exception->getMessage(), -1);
        }

        $userInfo = $jwtData['info']->getValue();
        //解密token中的用户信息
        $userInfo = str_encipher($userInfo,false, config('app.aes_token_key'));

        if (!$userInfo) {
            return shutdown(lang('user.loginError'), -1);
        }
        //解析json
        $userInfo = (array)json_decode($userInfo, true);
        
        if(cache('forbidUser_'.$userInfo['id'])){
            JWTAuth::invalidate(JWTAuth::token()->get());
            Cache::delete('forbidUser_'.$userInfo['id']);
            return shutdown(lang('user.forbid'), -1);
        }
        //已经登陆，将用户信息存入请求头
        $request->userInfo  = $userInfo;
        $request->uid       = $userInfo['id'];
        $request->userToken = JWTAuth::token()->get();
        return $next($request);
    }
}