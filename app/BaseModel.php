<?php
/**
 * Created by PhpStorm
 * User xiekunyu@kaishanlaw.com
 * Date 2021/7/9 16:15
 */

namespace app;

use think\facade\Db;
use think\Model;

class BaseModel extends Model
{
    protected        $defaultSoftDelete = 0;
    protected        $error             = '';
    protected static $db_prefix         = 'yu_';
    protected static $userInfo          = null;
    protected static $uid          = null;


    protected static function init()
    {
        self::$db_prefix = config('database.connections.mysql.prefix') ?: "yu_";
        self::initModel();
    }

    // 加载模型自动处理
    public static function initModel()
    {
        self::$userInfo=request()->userInfo ?? null;
        self::$uid=request()->userInfo['user_id'] ?? null;
    }

    /**
     * 获取树状信息
     * @param array $config
     */
    public static function getCheckNode($arr, $pid, $field = "parent_id", $table = '')
    {
        if (!$table) {
            $res = self::find($pid);
        } else {
            $res = Db::name($table)->find($pid);
        }
        if ($res) {
            if ($res[$field] > 0) {
                array_unshift($arr, $res[$field]);
                return self::getCheckNode($arr, $res[$field], $field, $table);
            }
        }
        return $arr;
    }

    // 获取错误信息
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取模型的json字段数组
     * @return array
     */
    public function getJsonFieldName(): array
    {
        return $this->json;
    }

     // 匹配列表信息
     public static function filterIdr($data, $many, $field)
     {
         if ($many) {
             $idr = \utils\Arr::arrayToString($data, $field, false);
         } else {
             $idr = [];
             if (is_array($field)) {
                 foreach ($field as $v) {
                     $idr[] = $data[$v];
                 }
             } else {
                 $idr = [$data[$field]];
             }
         }
         $key = array_search(0, $idr);
         if ($key) {
             array_splice($idr, $key, 1);
         }
         $idr  = array_unique($idr);
 
         return $idr ? : [];
     }

    //  获取某一项数据的统计
     public static function getTotal($map,$where=[],$field,$group){
        return self::field($field)
            ->where($map)
            ->where($where)
            ->group($group)
            ->select()->toArray();
    }
    
}