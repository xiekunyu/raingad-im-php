<?php
/* 正则集合
Created by wanghong<1204772286@qq.com>
Date: 2021-02-24 */

namespace utils;

class Regular{

    /**
     * 判断是否示手机号
     */
    public static function is_phonenumber($str){
        $preg ="/^1[3456789]\d{9}$/";
        return preg_match($preg,$str) ? true : false;
    }

    //判断是否是邮箱
    public static function is_email($str){
        $preg='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        return preg_match($preg,$str) ? true : false;
    }

    // 判断手机号或者邮箱,1手机号，2邮箱，0不是
    public static function check_account($str){
        if(self::is_phonenumber($str)){
            return 1;
        }
        if(self::is_email($str)){
            return 2;
        }
        return 0;
    }

    //判断是否是网址
    public static function is_url($str){
        $preg='/^((ht|f)tps?):\/\/([\w\-]+(\.[\w\-]+)*\/)*[\w\-]+(\.[\w\-]+)*\/?(\?([\w\-\.,@?^=%&:\/~\+#]*)+)?/';
        return preg_match($preg,$str) ? true : false;
    }

    /**
     * 摘取手机号
     * @param string $oldStr
     * @return array
     */
    public static function findThePhoneNumbers($oldStr = "",$onlyone=true){
        // 检测字符串是否为空
        $oldStr=trim("q{$oldStr}q");
        if(empty($oldStr)){
            return false;
        }
        $strArr = explode("-", $oldStr);
        $newStr = $strArr[0];
        for ($i=1; $i < count($strArr); $i++) {
            if (preg_match("/\d{2}$/", $newStr) && preg_match("/^\d{11}/", $strArr[$i])){
                $newStr .= $strArr[$i];
            } elseif (preg_match("/\d{3,4}$/", $newStr) && preg_match("/^\d{7,8}/", $strArr[$i])) {
                $newStr .= $strArr[$i];
            } else {
                $newStr .= "-".$strArr[$i];
            }
        }
        // 手机号的获取
        $reg='/\D(?:86)?(\d{11})\D/is';//匹配数字的正则表达式
        preg_match_all($reg,$newStr,$result);
		
        $nums = array();
        $common = '/^1[3-9]\d{9}$/';
        foreach ($result[1] as $key => $value) {
            if(preg_match($common,$value)){
                $nums[] = $value;
            }
        }
		if(count($nums)>0){
			return $onlyone ? $nums[0] : $nums;
		}else{
			return false;
		}
    }

    // 验证身份证号码
    public static function is_idcard($str){
        $reg = '/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/i';
        return preg_match($reg,$str) ? true : false;
    }
}