<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://tpadmin.yuan1994.com/
 * @copyright 2016 yuan1994 all rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
// 根据user-agent获取浏览器版本，操作系统
//-------------------------
class Agent
{
    public static function getVesion($v){
        if(isset($v[1])){
            $version= $v[1];
        }else{
            $version=0;
        }
        return $version;
    }
    /**
     * 获取客户端浏览器信息 添加win10 edge浏览器判断
     * @param  null
     * @author  Jea杨
     * @return string
     */
    public static function getBroswer()
    {
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $v);
            $exp[0] = "Firefox";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $v);
            $exp[0] = "傲游";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $v);
            $exp[0] = "IE";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $v);
            $exp[0] = "Opera";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $v);
            $exp[0] = "Edge";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $v);
            $exp[0] = "Chrome";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            preg_match("/rv:([\d\.]+)/", $sys, $v);
            $exp[0] = "IE";
            $exp[1]=self::getVesion($v);
        } elseif (stripos($sys, 'Safari') > 0) {
            preg_match("/safari\/([^\s]+)/i", $sys, $v);
            $exp[0] = "Safari";
            $exp[1]=self::getVesion($v);
        } else {
            $exp[0] = "未知浏览器";
            $exp[1] = "0";
        }
        return $exp[0] . '(' . $exp[1] . ')';
    }

    /**
     * 获取客户端操作系统信息包括win10
     * @param  null
     * @author  Jea杨
     * @return string
     */
    public static function getOs()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';#添加win10判断
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } else if (preg_match('/Mac/i', $agent)) {
            $os = 'Mac';
        } else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } else if (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } else if (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } else if (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } else if (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } else if (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } else if (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } else if (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } else if (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } elseif (preg_match('/ucweb|MQQBrowser|J2ME|IUC|3GW100|LG-MMS|i60|Motorola|MAUI|m9|ME860|maui|C8500|gt|k-touch|X8|htc|GT-S5660|UNTRUSTED|SCH|tianyu|lenovo|SAMSUNG/i', $agent)) {
            $os = 'mobile';
        } else {
            $os = '未知操作系统';
        }
        return $os;
    }
}
