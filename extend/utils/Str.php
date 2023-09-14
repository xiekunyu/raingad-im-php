<?php
/* file: 字符串处理类
Created by wanghong<1204772286@qq.com>
Date: 2021-02-22 */

namespace utils;

class Str{
    protected static $snakeCache = [];

    protected static $camelCache = [];

    protected static $studlyCache = [];

    /**
     * 检查字符串中是否包含某些字符串
     * @param string       $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串结尾
     *
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === static::substr($haystack, -static::length($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串开头
     *
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取指定长度的随机字母数字组合的字符串
     *
     * @param  int $length
     * @return string
     */
    public static function random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return static::substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 字符串转小写
     *
     * @param  string $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param  string $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * 获取字符串的长度
     *
     * @param  string $value
     * @return int
     */
    public static function length($value)
    {
        return mb_strlen($value);
    }

    /**
     * 截取字符串
     *
     * @param  string   $string
     * @param  int      $start
     * @param  int|null $length
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * 驼峰转下划线
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * 下划线转驼峰(首字母小写)
     *
     * @param  string $value
     * @return string
     */
    public static function camel($value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * 下划线转驼峰(首字母大写)
     *
     * @param  string $value
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * 转为首字母大写的标题格式
     *
     * @param  string $value
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /* 
    在数组中取想要的值
    $array -array 被取值的数组
    $field -string 所取的字段
    $is_str -boolean 返回字符串（默认）或数组
    return string
    */
    public static function array_to_string($array,$field,$is_str=true){
        $arr=[];
        foreach($array as $k => $v){
            if($v[$field]){
                $arr[]=$v[$field];
            }
        }
        //$idArr = array_unique($idArr);
        if($is_str){
            return implode(',',$arr);
        }else{
            return $arr;
        }

    }

    /* 
    密码生成规则
    $password -string 要转化的字符串
    return string
    */
    public static function password_hash_tp($password)
    {
        return hash("md5", trim($password));
    }

    /* 
    字符串截取函数
    $str -string 被截取的字符串
    $start -int 起始位置
    $length -int 截取长度
    $charset -string 编码
    $suffix -boolean 在$str的结尾拼接省略号（默认true）
    return string
    */
    public static function msubstr($str, $start, $length, $charset = "utf-8", $suffix = true)
    {
        if (strlen($str) / 3 > $length) {
            if (function_exists("mb_substr")) {
                if ($suffix == false) {
                    return mb_substr($str, $start, $length, $charset) . '&nbsp;...';
                } else {
                    return mb_substr($str, $start, $length, $charset);
                }
            } elseif (function_exists('iconv_substr')) {
                if ($suffix == false) {
                    return iconv_substr($str, $start, $length, $charset) . '&nbsp;...';
                } else {
                    return iconv_substr($str, $start, $length, $charset);
                }
            }
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
            if ($suffix) {
                return $slice;
            } else {
                return $slice;
            }
        }
        return $str;
    }

    /* 
    获取指定长度的随机字符串
    $length 取值长度
    return string
     */
    public static function get_rand_char($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /* 
    隐藏电话中间四位数
    $num -string 电话号码
    return string
    */
    public static function hide_phone($num)
    {
        return substr($num, 0, 3) . '****' . substr($num, 7);
    }

    /* 
    匹配特殊字符
    $word -string 匹配特殊字符
    */
    public static function match_special_str($word)
    {
        if (preg_match("/[\'.,:;*?~`!@#$%^&+=<>{}]|\]|\[|\/|\\\|\"|\|/", $word)) {
            //不允许特殊字符
            return true;
        } else {
            return false;
        }
    }




// +----------------------------------------------------------------------
// 数据加密处理
// +----------------------------------------------------------------------

    //id加密
    public static function encryption($str)
    {
        $hash = config('hashids');
        return hashids($hash['length'], $hash['salt'])->encode($str);
    }

    //id解密
    public static function decrypt($str)
    {
        $hash = config('hashids');
        return hashids($hash['length'], $hash['salt'])->decode($str);
    }

    //token加密
    public static function encryptionToken($id)
    {
        $str = encryption($id);
        $time = md5(strtotime(date('Y-m-d')));
        $str = base64_encode($str . '-' . $time);
        return $str;
    }

    //token解密
    public static function decryptToken($str)
    {
        $str = base64_decode($str);
        $arr = explode('-', $str);
        $time = md5(strtotime(date('Y-m-d')));
        if ($arr[1] != $time) {
            return false;
        }
        return decrypt($arr[0]);
    }

    /* @param string $string 原文或者密文
    * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
    * @param string $key 密钥
    * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
    * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
    *
    * @example
    *
    *  $a = authcode('abc', 'ENCODE', 'key');
    *  $b = authcode($a, 'DECODE', 'key');  // $b(abc)
    *
    *  $a = authcode('abc', 'ENCODE', 'key', 3600);
    *  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
    */
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {

        $ckey_length = 4;   
        // 随机密钥长度 取值 0-32;
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥

        $key = md5($key ? $key : 'default_key'); //这里可以填写默认key值
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        if($operation == 'DECODE') {
                if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                    return substr($result, 26);
                } else {
                    return '';
                }
            } else {
                return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    public static function ssoTokenEncode($str,$key='lvzhesso',$expire=0){
        $ids=encryption($str);
    return authcode($ids,"ENCODE",$key,$expire);
    }

    public static function ssoTokenDecode($str,$key='lvzhesso')
    {
        $ids=authcode($str,"DECODE",$key);
        try{
            return decrypt($ids);
        }catch(\Exception $e){
            return '';
        }
    }

    /* 
    获取url中的主机名
    $url -string 
    return string
    */
    public static function getHost($url)
    { 
        if(!preg_match('/http[s]:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){
            return '';
        }
        $search = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
        $url = trim($url);
        preg_match_all($search, $url ,$rr);
        return $rr[4][0];

    }

    /* 
    替换特殊字符
    $url -string 
    return string
    */
    public static function strFilter($str,$is_file=false){
        $str = str_replace('`', '', $str);
        $str = str_replace('·', '', $str);
        $str = str_replace('~', '', $str);
        $str = str_replace('!', '', $str);
        $str = str_replace('！', '', $str);
        $str = str_replace('@', '', $str);
        $str = str_replace('#', '', $str);
        $str = str_replace('$', '', $str);
        $str = str_replace('￥', '', $str);
        $str = str_replace('%', '', $str);
        $str = str_replace('……', '', $str);
        $str = str_replace('&', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = str_replace('（', '', $str);
        $str = str_replace('）', '', $str);
        $str = str_replace('-', '', $str);
        $str = str_replace('_', '', $str);
        $str = str_replace('——', '', $str);
        $str = str_replace('+', '', $str);
        $str = str_replace('=', '', $str);
        $str = str_replace('|', '', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('【', '', $str);
        $str = str_replace('】', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace(';', '', $str);
        $str = str_replace('；', '', $str);
        $str = str_replace(':', '', $str);
        $str = str_replace('：', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('“', '', $str);
        $str = str_replace('”', '', $str);
        $str = str_replace(',', '', $str);
        $str = str_replace('，', '', $str);
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('《', '', $str);
        $str = str_replace('》', '', $str);
        $str = str_replace('。', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace('、', '', $str);
        $str = str_replace('?', '', $str);
        $str = str_replace('？', '', $str);
        if(!$is_file){
            $str = str_replace('.', '', $str);
        }
        return trim($str);
    }

    /**
     * 隐藏公司名或人名的中间部分
     * @param string $str 需要处理的字符串
     * @return string 处理后的字符串
     */
    public static function maskString($str,$i=3) {
        // 获取字符串长度
        $len = self::get_string_length($str);
        
        // 如果数组长度小于等于2，则只将第二个字符替换为*
        if ($len <= 1) {
            return '******';
        }elseif ($len == 2) {
            return self::msubstr($str,0,1).'*';
        } else {
            return self::msubstr($str,0,$i).'******'.self::msubstr($str,-$i,$i);
        }
    }

    /**
     * 获取人名的最后一个字或者两个字
     * @param string $str 需要处理的字符串
     * @return string 处理后的字符串
     */
    public static function getLastName($str,$i=1) {
        // 获取字符串长度
        $len = self::get_string_length($str);
        
        // 如果数组长度小于等于2，则只将第二个字符替换为*
        if ($len < 2) {
            return self::msubstr($str,0,1);
        }else{
            return self::msubstr($str,-$i,$i);
        }
    }

    public static function get_string_length($str) {
        // 将字符串转换为 UTF-8 编码
        $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
        // 返回字符串的字符数
        return mb_strlen($str);
    }

    // 提取身份证中的年龄和性别
    public static function getIdforAG($str){
        if(!$str){
            return false;
        }
        // 先验证是否为身份证
        if(!preg_match('/(^\d{15}$)|(^\d{17}([0-9]|X)$)/',$str)){
            return false;
        }
        $length=strlen($str);
        if($length==15){
            $sexnum = substr($str,14,1);
            $age = date('Y') - '19'.substr($str,6,2);
        }else{
            $sexnum = substr($str,16,1);
            $age = date('Y') - substr($str,6,4);
        }
        return [
            'gender'=>$sexnum%2==0 ? 0 : 1,
            'age'=>$age
        ];
    }

    /**
    * Universally Unique Identifier v4
    *
    * @param  int   $b
    * @return UUID, if $b returns binary(16)
    */
    public static function getUuid($b = null)
    {
        if (function_exists('uuid_create')) {
            $uuid = uuid_create();
        } else {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xfff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        }
        return $b ? pack('H*', str_replace('-', '', $uuid)) : $uuid;
    }
}