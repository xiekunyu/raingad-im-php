<?php
/* file:常用函数、工具类函数封装的集合
Created by wanghong<1204772286@qq.com>
Date: 2021-02-24 */

namespace utils;

class Utils{

    /*
    * 文件根据类型进行分类
    $ext -string 文件后缀
    return -int
    * */
    public static function getFileType($ext){
        $ext=strtolower($ext);
        $image=['jpg','jpeg','png','bmp','gif'];
        $radio=['mp3','wav','wmv','amr'];
        $video=['mp4','3gp','avi','m2v','mkv','mov'];
        $doc=['ppt','pptx','doc','docx','xls','xlsx','pdf','txt','md'];
        if(in_array($ext,$doc)){
            $fileType=1;
        }elseif(in_array($ext,$image)){
            $fileType=2;
        }elseif(in_array($ext,$radio)){
            $fileType=3;
        }elseif(in_array($ext,$video)){
            $fileType=4;
        }else{
            $fileType=9;
        }
        return $fileType;
    }


    /* 
    获取文件的大小
    */
    public static function get_file_size($size, $limit = 0)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2) . $units[$i + $limit];
    }

    /* 
    h5ip网址缩短
    $link -string
    return string
    */
    function h5ipUrl($link)
    {
        $url = "http://h5ip.cn/index/api?format=json&url=" . $link;
        $data = json_decode(curl_request($url), true);
        if ($data['code'] == 0) {
            return $data['short_url'];
        } else {
            return $link;
        }
    }

    /* 
    linux系统探测
    */
    function sys_linux()
    {
        // CPU
        if (false === ($str = @file("/proc/cpuinfo"))) return false;
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
        @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
        if (false !== is_array($model[1])) {
            $res['cpu']['num'] = sizeof($model[1]);
            for ($i = 0; $i < $res['cpu']['num']; $i++) {
                $res['cpu']['model'][] = $model[1][$i] . '&nbsp;(' . $mhz[1][$i] . ')';
                $res['cpu']['mhz'][] = $mhz[1][$i];
                $res['cpu']['cache'][] = $cache[1][$i];
                $res['cpu']['bogomips'][] = $bogomips[1][$i];
            }
            if ($res['cpu']['num'] == 1)
                $x1 = '';
            else
                $x1 = ' ×' . $res['cpu']['num'];
            $mhz[1][0] = ' | 频率:' . $mhz[1][0];
            $cache[1][0] = ' | 二级缓存:' . $cache[1][0];
            $bogomips[1][0] = ' | Bogomips:' . $bogomips[1][0];
            $res['cpu']['model'][] = $model[1][0] . $mhz[1][0] . $cache[1][0] . $bogomips[1][0] . $x1;
            if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
            if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
            if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
            if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
        }
        // NETWORK
        // UPTIME
        if (false === ($str = @file("/proc/uptime"))) return false;
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days . "天";
        if ($hours !== 0) $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

        // MEMORY
        if (false === ($str = @file("/proc/meminfo"))) return false;
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

        $res['memTotal'] = round($buf[1][0] / 1024, 2);
        $res['memFree'] = round($buf[2][0] / 1024, 2);
        $res['memBuffers'] = round($buffers[1][0] / 1024, 2);
        $res['memCached'] = round($buf[3][0] / 1024, 2);
        $res['memUsed'] = $res['memTotal'] - $res['memFree'];
        $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;
        $res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
        $res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
        $res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] / $res['memTotal'] * 100, 2) : 0; //真实内存使用率
        $res['memCachedPercent'] = (floatval($res['memCached']) != 0) ? round($res['memCached'] / $res['memTotal'] * 100, 2) : 0; //Cached内存使用率
        $res['swapTotal'] = round($buf[4][0] / 1024, 2);
        $res['swapFree'] = round($buf[5][0] / 1024, 2);
        $res['swapUsed'] = round($res['swapTotal'] - $res['swapFree'], 2);
        $res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;

        // LOAD AVG
        if (false === ($str = @file("/proc/loadavg"))) return false;
        $str = explode(" ", implode("", $str));
        $str = array_chunk($str, 4);
        $res['loadAvg'] = implode(" ", $str[0]);
        return $res;
    }

    /**
     * 将数组按字母A-Z排序
     * return array
     */
    function arry_sort($array, $field)
    {
        $newArray = [];
        foreach ($array as $k => &$v) {
            $v['chart'] = getFirstChart($v[$field]);
            $newArray[] = $v;
        }
        $data = [];
        foreach ($newArray as $k => $v) {
            if (array_key_exists($v['chart'], $data)) {
                $data[$v['chart']][] = $v;
            } else {
                $data[$v['chart']] = [];
                $data[$v['chart']][] = $v;
            }
        }
        ksort($data); 
        return $data;
    }



}