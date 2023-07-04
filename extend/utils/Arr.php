<?php
/* file: 数组处理类
Created by wanghong<1204772286@qq.com>
Date: 2021-02-23 */

namespace utils;

class Arr{

    /**
     * 节点遍历
     * @param array $list 遍历的数组
     * @param string $pk 主键id
     * @param string $pid 父id
     * @param string $child 子数组
     * @param int $root 判断是否存在parent
     * @return array
     **/
    public static function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }else {
                        $tree[] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * 删除重复的二维数组
     * $array 需要操作的数组
     * $field 根据字段进行对比
     * return array
     */
    public static function remove_duplicate($array, $field)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $has = false;
            foreach ($result as $val) {
                if ($val[$field] == $value[$field]) {
                    $has = true;
                    break;
                }
            }
            if (!$has) {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
    * 二维数组取最大值
    * $array 操作的数组
    * $field 取某个字段的最大值
    * $returnArr 返回最大的值(默认)或者最大值所在的数组
    */
    public static function get_array_max($array, $field, $returnArr = false)
    {
        if(!$array){
            return 0;
        }
        foreach ($array as $k => $v) {
            $temp[] = $v[$field];
        }
        if ($returnArr) {
            $max = max($temp);
            foreach ($array as $k => $v) {
                if ($v[$field] == $max) {
                    return $v;
                    break;
                }
            }
        } else {
            return max($temp);
        }
    }


    /*
    * 二维数组排序
    * $arrays 需要排序的数组
    * $sort_key 需要排序的字段
    * $sort_order 正序(默认)还是倒序
    * $sort_type  排序的类型:数字(默认),字母
    * return $array
    */
    public static function sort_array($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC){
        if (is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    $key_arrays[] = $array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
        return $arrays;
    }

    /*
     * 查询二维数组中是否含有此值
     * $value 所需的值
     * $array 操作的数组
     * return boolean
     */
    public static function deep_in_array($value, $array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }
            if (in_array($value, $item)) {
                return true;
            } else if (deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 将相同值的二维数组重组一个新的三维数组
     * $field 需要放到一个数组的字段
     * $title 作为新数组的标题
     * $array 需要处理的数组
     * */
    //将相同值的二维数组重组一个新的数组。
    public static function recombine_array($field,$title,$array,$name='name',$list='dataList'){
        $data=[];
        foreach ($array as $k => $v) {
            $arr[]=$v[$field];
            $arr=array_unique($arr);
            $num=0;
            foreach($arr as $key=>$val){
                if($v[$field]==$val){
                    $data[$num][$name] = $v[$title];
                    $data[$num][$list][] = $v;
                }
                ++$num;
            }
        }
        return $data;
    }

    /*
    将object转array
     $array 要转化的对象
     return array
     */
    public static function object_array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = object_array($value);
            }
        }
        return $array;
    }

    /*
    json转array
    $json 要转化的json串
    return array
    */
    public static function json_to_array($json)
    {
        $array = json_decode($json);
        $arr = [];
            if ($array) {
                foreach ($array as $k => $v) {
                    $arr[] = object_array($v);
                }
        }
        return $arr;
    }

    /*
    数组中查找相应的值,只要出现一次即返回,否则返回false;
    $array 被查找的数组
    $name 要查找的字段
    $condition 匹配条件
    return array
    */
    public static function query_array($array, $name, $condition,$key='')
    {
        if (!is_array($array)) {
            return false;
        }
        foreach ($array as $item) {
            if ($item[$name] == $condition) {
                if($key){
                    return $item[$key];
                }
                return $item;
            }
        }
        return false;
    }

    /*
    在数组中查找相应的值,将查找到的结果集全部返回,如果没有找到,则返回false.
    $array 查找的数组
    $name 查找的字段
    $condition 匹配条件
    return array
    */
    public static function query_array_all($array, $name, $condition)
    {
        if (!is_array($array)) {
            return false;
        }
        $returnArray = array();
        foreach ($array as $item) {
            if ($item[$name] == $condition) {
                $returnArray[] = $item;
            }
        }
        if (count($returnArray) > 0) {
            return $returnArray;
        } else {
            return false;
        }
    }

    /* 
    获取两个数字之间的值，形成一个新的数组
    $from 起始值
    $to 终止值
    $step -int 步长
    $str -string 数字结尾处拼接的字符串
    return array
    */
    public static function array_range($from, $to, $step=1,$str=''){
        $array = array();
        for ($x=$from; $x <= $to; $x += $step){
            $array[] = $x.$str;
        }
        return $array;
    }

            //数组中获取ID字符串
    public static function arrayToString($array, $field, $isStr = true)
    {
        $idArr = [];
        foreach ($array as $k => $v) {
            if(is_array($field)){
                foreach($field as $val){
                    $idArr[]=$v[$val];
                }
            }else{
                $idArr[] = $v[$field];
            }
        }
        if ($isStr) {
            $idStr = implode(',', $idArr);
            return $idStr;
        } else {
            return $idArr;
        }
    }

    /**
     * 用数组中某个字段的值 作为数组的键
     * @param array $arr 需要处理的数组
     * @param string $keyValue 作为键的值
     * @return array
     */
    public static function array_value_key($arr, $keyValue)
    {
        $temp = [];
        foreach ($arr as $item) {
            if (isset($item[$keyValue])){
                $temp[$item[$keyValue]] = $item;
            }
        }
        return $temp;
    }

}