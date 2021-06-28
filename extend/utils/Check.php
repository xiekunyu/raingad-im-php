<?php
/* file:常用验证函数封装的集合
Created by wanghong<1204772286@qq.com>
Date: 2021-02-25 */

namespace utils;

class Check{

    /* 
    判断是否示手机号
    $phone -string
    return boolean
    */
    public static function check_phone($phone)
    {
        $check ="/^1[3456789]\d{9}$/";
        if (preg_match($check, $phone)) {
            return true;
        } else {
            return false;
        }
    }

    /* 
    判断是否是邮箱
    $email -string
    return boolean
    */
    public static function check_email($email)
    {
        $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        if(preg_match($preg_email,$email)){
            return true;
        } else {
            return false;
        }
    }

    /* 
    判断是否是一个IP
    $str -string
    return 
    */
    public static function check_ip($str)
    {
        $ip = explode('.', $str);
        for ($i = 0; $i < count($ip); $i++) {
            if ($ip[$i] > 255) {
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
    }

    


}