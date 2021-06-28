<?php
/* file:时间处理函数封装的集合
Created by wanghong<1204772286@qq.com>
Date: 2021-02-25 */

namespace utils;

class Time{
    /**
     * 获取指定年月的开始和结束时间戳
     * @param int $y    年份
     * @param int $m    月份
     * @return array(开始时间,结束时间)
     */
    public static function time_start_end($y=0,$m=0)
    {
        $y = $y ? $y : date('Y');
        $m = $m ? $m : date('m');
        $d = date('t', strtotime($y.'-'.$m));
        return array("firsttime"=>strtotime($y.'-'.$m),"lasttime"=>mktime(23,59,59,$m,$d,$y));
    }

    /* 
    获取多少个工作日以后的日期
    $cost -int
    $start -int
    return string
    */
    public static function get_all_day($cost,$start='')
    {
        $workday = array(1,2,3,4,5);
        // 需要多少天
        $days = 0;

        if($start==''){
            $curday = time();
        }else{
            $curday = $start;
        }
        // 获取当前是星期几
        while($cost>=0){
            if(in_array(date('w',$curday),$workday)){ //是工作日
                $cost--;
            }
            $days++;
            if($cost>=0){
                $curday = mktime(date("H"),date("i"),date("s"),date('m',$curday),date('d',$curday)+1,date('Y',$curday));
            }
        }
        return date('Y-m-d H:i',$curday);
    }


    /* 
    获取当前时间戳毫秒
    */
    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /* 
    数据发布时间
    return string
    */
    public static function from_time($time)
    {
        $way = time() - (int)$time;
        if ($way < 60) {
            $r = '刚刚';
        } elseif ($way >= 60 && $way < 3600) {
            $r = floor($way / 60) . '分钟前';
        } elseif ($way >= 3600 && $way < 86400) {
            $r = floor($way / 3600) . '小时前';
        } elseif ($way >= 86400 && $way < 2592000) {
            $r = floor($way / 86400) . '天前';
        } elseif ($way >= 2592000 && $way < 15552000) {
            $r = floor($way / 2592000) . '个月前';
        } elseif ((int)$time == 0) {
            $r = '无';
        } else {
            $r = date('Y-m-d H:i', (int)$time);
        }
        return $r;
    }

    /* 
    获取多少天后
    return -string
    */
    public static function get_days_ago($time)
    {
        $way = $time - time();
        $a = date('Y-m-d', $time);
        $b = date('Y-m-d', time());
        $c = date('Y-m-d', time() + 24 * 3600);
        if ($way >= 86400 && $way < 2592000) {
            $r = floor($way / 86400) . '天后';
        } elseif ($way >= 2592000 && $way < 15552000) {
            $r = floor($way / 2592000) . '个月后';
        } elseif ($time < strtotime($b)) {
            $day = abs(ceil($way / 86400));
            if ($time == 0) {
                $r = "暂无";
            } elseif ($day < 90) {
                $r = '已过期' . $day . "天";
            } else {
                $r = "已过期3月+";
            }
        } elseif ($a == $b) {
            $r = '今天内';
        } elseif ($a == $c) {
            $r = '明天';
        } else {
            $r = date('Y-m-d', $time);
        }
        return $r;
    }

    /* 
    获取待办的时间
    return $str
    */
    public static function schdule_time($time)
    {
        $date = date("Y-m-d", $time);
        $today = date("Y-m-d");
        $tomorrow = date("Y-m-d", time() + 24 * 3600);
        $future = time() + 2 * 24 * 3600;
        if ($date == $today) {
            $str = "今天";
        } elseif ($date == $tomorrow) {
            $str = "明天";
        } elseif ($time >= $future) {
            $str = "未来几天";
        } else {
            $str = "已过期";
        }
        return $str;
    }

    /* 
    获取多少天
    return int
    */
    public static function days_num($time)
    {
        $way = time() - $time;
        return floor($way / 86400);
    }

    /* 
    获取多少小时
    return float
    */
    public static function hours_num($time)
    {
        return round($time / 60, 1);
    }

    /* 
    获取多少天后
    return string
    */
    public static function days_ago($daytime)
    {
        $daysago = ceil(($daytime - time()) / (3600 * 24));
        return $daysago . "天后";
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    public static function between_two_days($second1, $second2)
    {
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        $day = floor(($second1 - $second2) / 86400);
        return $day;
    }

    /**
     * 获取本周所有日期
     * $time -起始日期
     * $format -输出格式
     * return array
     */
    public static function get_week($time = '', $format='Y-m-d')
    {
        $time = $time != '' ? $time : time();
        //获取当前周几
        $week = date('w', $time);
        $date = [];
        for ($i=1; $i<=7; $i++){
        $date[$i] = date($format ,strtotime( '+' . $i-$week .' days', $time));
        }
        return $date;
    }

    /* 
    获取上周的开始时间和结束日期
    return array
    */
    function get_last_week()
    {
        $curr = date("Y-m-d"); 
        $w=date('w',time());//获取当前周的第几天 周日是 0 周一到周六是1-6  
        $endTime=strtotime($curr.' -'.($w ? $w-1 : 6).' days');//获取本周开始日期，如果$w是0是周日:-6天;其它:$w-1天   
        $startTime=strtotime(date('Y-m-d',strtotime(date('Y-m-d',$endTime)." -7 days")));
        return [$startTime,$endTime];
    }
    

}