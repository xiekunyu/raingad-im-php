<?php
/* file:时间处理函数封装的集合
Created by wanghong<1204772286@qq.com>
Date: 2021-02-25 */

namespace utils;

class Time{

    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today()
    {
        list($y, $m, $d) = explode('-', date('Y-m-d'));
        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y)
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday()
    {
        $yesterday = date('d') - 1;
        return [
            mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
            mktime(23, 59, 59, date('m'), $yesterday, date('Y'))
        ];
    }

     /**
     * 返回明日开始和结束的时间戳
     *
     * @return array
     */
    public static function tomorrow()
    {
        $tomorrow = date('d') + 1;
        return [
            mktime(0, 0, 0, date('m'), $tomorrow, date('Y')),
            mktime(23, 59, 59, date('m'), $tomorrow, date('Y'))
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week()
    {
        list($y, $m, $d, $w) = explode('-', date('Y-m-d-w'));
        if($w == 0) $w = 7; //修正周日的问题
        return [
            mktime(0, 0, 0, $m, $d - $w + 1, $y), mktime(23, 59, 59, $m, $d - $w + 7, $y)
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp))),
            strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + 24 * 3600 - 1
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month($everyDay = false)
    {
        list($y, $m, $t) = explode('-', date('Y-m-t'));
        return [
            mktime(0, 0, 0, $m, 1, $y),
            mktime(23, 59, 59, $m, $t, $y)
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth()
    {
        $y = date('Y');
        $m = date('m');
        $begin = mktime(0, 0, 0, $m - 1, 1, $y);
        $end = mktime(23, 59, 59, $m - 1, date('t', $begin), $y);

        return [$begin, $end];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year()
    {
        $y = date('Y');
        return [
            mktime(0, 0, 0, 1, 1, $y),
            mktime(23, 59, 59, 12, 31, $y)
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear()
    {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year)
        ];
    }

    public static function dayOf()
    {

    }

    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @param int $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     * @return array
     */
    public static function dayToNow($day = 1, $now = true)
    {
        $end = time();
        if (!$now) {
            list($foo, $end) = self::yesterday();
        }

        return [
            mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
            $end
        ];
    }

    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAgo($day = 1)
    {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }

    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAfter($day = 1)
    {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }

    /**
     * 天数转换成秒数
     *
     * @param int $day
     * @return int
     */
    public static function daysToSecond($day = 1)
    {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $week
     * @return int
     */
    public static function weekToSecond($week = 1)
    {
        return self::daysToSecond() * 7 * $week;
    }

    private static function startTimeToEndTime()
    {

    }

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
        $time=is_string($time)?strtotime($time):$time;
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
    public static function get_last_week()
    {
        $curr = date("Y-m-d"); 
        $w=date('w',time());//获取当前周的第几天 周日是 0 周一到周六是1-6  
        $endTime=strtotime($curr.' -'.($w ? $w-1 : 6).' days');//获取本周开始日期，如果$w是0是周日:-6天;其它:$w-1天   
        $startTime=strtotime(date('Y-m-d',strtotime(date('Y-m-d',$endTime)." -7 days")));
        return [$startTime,$endTime];
    }
    
    /**
     * 根据时间戳计算当月天数
     * @param
     */
    public static function getmonthdays($time)
    {
        $month = date('m', $time);
        $year = date('Y', $time);
        if (in_array($month, array('1', '3', '5', '7', '8', '01', '03', '05', '07', '08', '10', '12'))) {
            $days = '31';
        } elseif ($month == 2) {
            if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0)) {
                //判断是否是闰年  
                $days = '29';
            } else {
                $days = '28';
            }
        } else {
            $days = '30';
        }
        return $days;
    }

    /**
     * 生成从开始时间到结束时间的日期数组
     * @param type，默认时间戳格式
     * @param type = 1 时，date格式
     * @param type = 2 时，获取每日开始、结束时间
     */
    public static function dateList($start, $end, $type = 0)
    {
        if (!is_numeric($start) || !is_numeric($end) || ($end <= $start)) return '';
        $i = 0;
        //从开始日期到结束日期的每日时间戳数组
        $d = array();
        if ($type == 1) {
            while ($start <= $end) {
                $d[$i] = date('Y-m-d', $start);
                $start = $start + 86400;
                $i++;
            }
        } else {
            while ($start <= $end) {
                $d[$i] = $start;
                $start = $start + 86400;
                $i++;
            }
        }
        if ($type == 2) {
            $list = array();
            foreach ($d as $k => $v) {
                $list[$k] = getDateRange($v);
            }
            return $list;
        } else {
            return $d;
        }
    }

    /**
     * 获取指定日期开始时间与结束时间
     */
    public static function getDateRange($timestamp)
    {
        $ret = array();
        $ret['sdate'] = strtotime(date('Y-m-d', $timestamp));
        $ret['edate'] = strtotime(date('Y-m-d', $timestamp)) + 86400;
        return $ret;
    }

    /**
     * 生成从开始月份到结束月份的月份数组
     * @param int $start 开始时间戳
     * @param int $end 结束时间戳
     */
    public static function monthList($start, $end)
    {
        if (!is_numeric($start) || !is_numeric($end) || ($end <= $start)) return '';
        $start = date('Y-m', $start);
        $end = date('Y-m', $end);
        //转为时间戳
        $start = strtotime($start . '-01');
        $end = strtotime($end . '-01');
        $i = 0;
        $d = array();
        while ($start <= $end) {
            //这里累加每个月的的总秒数 计算公式：上一月1号的时间戳秒数减去当前月的时间戳秒数
            $d[$i] = $start;
            $start += strtotime('+1 month', $start) - $start;
            $i++;
        }
        return $d;
    }

    /**
     * 将秒数转换为时间 (年、天、小时、分、秒）
     * @param
     */
    public static function getTimeBySec($time)
    {
        $t='';
        if (is_numeric($time)) {
            $value = array(
                "years" => 0, "days" => 0, "hours" => 0,
                "minutes" => 0, "seconds" => 0,
            );
            if ($time >= 31556926) {
                $value["years"] = floor($time / 31556926);
                $time = ($time % 31556926);
                $t .= $value["years"] . "年";
            }
            if ($time >= 86400) {
                $value["days"] = floor($time / 86400);
                $time = ($time % 86400);
                $t .= $value["days"] . "天";
            }
            if ($time >= 3600) {
                $value["hours"] = floor($time / 3600);
                $time = ($time % 3600);
                $t .= $value["hours"] . "小时";
            }
            if ($time >= 60) {
                $value["minutes"] = floor($time / 60);
                $time = ($time % 60);
                $t .= $value["minutes"] . "分钟";
            }
            if ($time < 60) {
                $value["seconds"] = floor($time);
                $t .= $value["seconds"] . "秒";
            }
            return $t;
        } else {
            return (bool)FALSE;
        }
    }

    /**
     * 根据类型获取上一类型时间戳数组
     */
    public static function getLstTimeByType($type = 'today')
    {
        switch ($type) {
            case 'yesterday' :
                $timeArr = self::yesterday();
                break;
            case 'week' :
                $timeArr = self::week();
                break;
            case 'lastWeek' :
                $timeArr = self::lastWeek();
                break;
            case 'month' :
                $timeArr = self::month();
                break;
            case 'lastMonth' :
                $timeArr = self::lastMonth();
                break;
            case 'quarter' :
                //本季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
                } else {
                    $daterange_start_time = strtotime(date('Y-10-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-12-31 23:59:59"));
                }
                $timeArr = array($daterange_start_time, $daterange_end_time);
                break;
            case 'lastQuarter' :
                //上季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $year = date('Y') - 1;
                    $daterange_start_time = strtotime(date($year . '-10-01 00:00:00'));
                    $daterange_end_time = strtotime(date($year . '-12-31 23:59:59'));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
                } else {
                    $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
                }
                $timeArr = array($daterange_start_time, $daterange_end_time);
                break;
            case 'year' :
                $timeArr = self::year();
                break;
            case 'lastYear' :
                $timeArr = self::lastYear();
                break;
            default :
                $timeArr = self::today();
                break;
        }
        return $timeArr;
    }


    /**
     * 根据类型获取开始结束时间戳数组
     * @param
     */
    public static function getTimeByType($type = 'today', $is_last = false)
    {
        $daterange_start_time_last_time='';
        $daterange_end_time_last_time='';
        $lastArr = [];
        switch ($type) {
            case 'yesterday' :
                $timeArr = self::yesterday();
                $lastArr = self::yesterday(1);
                break;
            case 'week' :
                $timeArr = self::week();
                $lastArr = self::lastWeek();
                break;
            case 'lastWeek' :
                $timeArr = self::lastWeek();
                $lastArr = self::lastWeek(1);
                break;
            case 'month' :
                $timeArr = self::month();
                $lastArr = self::lastMonth();
                break;
            case 'lastMonth' :
                $timeArr = self::lastMonth();
                $lastArr = self::lastMonth(1);
                break;
            case 'quarter' :
                //本季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
                } else {
                    $daterange_start_time = strtotime(date('Y-10-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-12-31 23:59:59"));
                }

                //上季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $year = date('Y') - 1;
                    $daterange_start_time_last_time = strtotime(date($year . '-10-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date($year . '-12-31 23:59:59'));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time_last_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time_last_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-06-30 23:59:59"));
                } else {
                    $daterange_start_time_last_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-09-30 23:59:59"));
                }
                $timeArr = array($daterange_start_time, $daterange_end_time);
                $lastArr = array($daterange_start_time_last_time, $daterange_end_time_last_time);
                break;
            case 'lastQuarter' :
                //上季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $year = date('Y') - 1;
                    $daterange_start_time = strtotime(date($year . '-10-01 00:00:00'));
                    $daterange_end_time = strtotime(date($year . '-12-31 23:59:59'));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
                } else {
                    $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
                }
                $timeArr = array($daterange_start_time, $daterange_end_time);
                $lastArr = array($daterange_start_time_last_time, $daterange_end_time_last_time);
                break;
            case 'year' :
                $timeArr = self::year();
                $lastArr = self::lastYear();
                break;
            case 'lastYear' :
                $timeArr = self::lastYear();
                $lastArr = self::lastYear(1);
                break;
            default :
                $timeArr = self::today();
                $lastArr = self::yesterday();
                break;
        }
        if ($is_last) {
            return $lastArr;
        } else {
            return $timeArr;
        }
    }

    /**
     * 图表时间范围处理，按月/天返回时间段数组
     *
     * @param int $start 开始时间（时间戳）
     * @param int $end 结束时间（时间戳）
     * @return array
     * @author Ymob
     * @datetime 2019-11-18 09:25:09
     */
    public static function getTimeArray($start = null, $end = null)
    {
        if ($start == null || $end == null) {
            $param = request()->param();
            switch ($param['type']) {
                // 本年
                case 'year':
                    $start = strtotime(date('Y-01-01'));
                    $end = strtotime('+1 year', $start) - 1;
                    break;
                // 去年
                case 'lastYear':
                    $start = strtotime(date(date('Y') - 1 . '-01-01'));
                    $end = strtotime('+1 year', $start) - 1;
                    break;
                // 本季度、上季度
                case 'quarter':
                case 'lastQuarter':
                    $t = intval((date('m') - 1) / 3);
                    $start_y = ($t * 3) + 1;
                    $start = strtotime(date("Y-{$start_y}-01"));
                    if ($param['type'] == 'lastQuarter') {  // 上季度
                        $start = strtotime('-3 month', $start);
                    }
                    $end = strtotime('+3 month', $start) - 1;
                    break;
                // 本月、上月
                case 'month':
                case 'lastMonth':
                    $start = strtotime(date('Y-m-01'));
                    if ($param['type'] == 'lastMonth') {
                        $start = strtotime('-1 month', $start);
                    }
                    $end = strtotime('+1 month', $start) - 1;
                    break;
                // 本周、上周
                case 'week':
                case 'lastWeek':
                    $start = strtotime('-' . (date('w') - 1) . 'day', strtotime(date('Y-m-d')));
                    if ($param['type'] == 'lastWeek') {
                        $start = strtotime('-7 day', $start);
                    }
                    $end = strtotime('+7 day', $start) - 1;
                    break;
                // 今天、昨天
                case 'today':
                case 'yesterday':
                    $start = strtotime(date('Y-m-d'));
                    if ($param['type'] == 'yesterday') {
                        $start = strtotime('-1 day', $start);
                    }
                    $end = strtotime('+1 day', $start) - 1;
                    break;
                default:
                    if ($param['start_time'] && $param['end_time']) {
                        $start = $param['start_time'];
                        $end = $param['end_time'];
                    } else {
                        // 本年
                        $start = strtotime(date('Y-01-01'));
                        $end = strtotime('+1 year', $start) - 1;
                    }
                    break;
            }
        }

        $between = [$start, $end];
        $list = [];
        $len = ($end - $start) / 86400;
        // 大于30天 按月统计、小于按天统计
        if ($len > 31) {
            $time_format = '%Y-%m';
            while (true) {
                $start = strtotime(date('Y-m-01', $start));
                $item = [];
                $item['type'] = date('Y-m', $start);
                $item['start_time'] = $start;
                $item['end_time'] = strtotime('+1 month', $start) - 1;
                $list[] = $item;
                if ($item['end_time'] >= $end) break;
                $start = $item['end_time'] + 1;
            }
        } else {
            $time_format = '%Y-%m-%d';
            while (true) {
                $item = [];
                $item['type'] = date('Y-m-d', $start);
                $item['start_time'] = $start;
                $item['end_time'] = strtotime('+1 day', $start) - 1;
                $list[] = $item;
                if ($item['end_time'] >= $end) break;
                $start = $item['end_time'] + 1;
            }
        }

        return [
            'list' => $list,        // 时间段列表
            'time_format' => $time_format,      // 时间格式 mysql 格式化时间戳
            'between' => $between       // 开始结束时间
        ];
    }

    /**
     * 简化时间的展示，可传入时间戳或者日期
     */
    public static function simpleTime($time){
        $time=is_string($time) ? strtotime($time) : $time;
        if($time==0){
            return "--";
        }
        $today=date("Y-m-d",$time);
        $year=date("Y",$time);
        if($today==date("Y-m-d",time())){
            return date('H:i',$time);
        }elseif($year==date("Y",time())){
            return date('m-d H:i',$time);
        }else{
            return date('Y-m-d H:i',$time);
        }
    }

    // 根据秒数获取时长
    public static function getDuration($seconds) 
    { 
        $hour = floor($seconds / 3600); 
        $min = floor(($seconds % 3600) / 60); 
        $sec = $seconds % 60; 
    
        if ($hour > 0) { 
            return $hour . "小时" . $min . "分" . $sec . "秒"; 
        } elseif ($min > 0) { 
            return $min . "分" . $sec . "秒"; 
        } else { 
            return $sec . "秒"; 
        } 
    } 
    
}