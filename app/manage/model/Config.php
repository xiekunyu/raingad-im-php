<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\manage\model;

use think\Model;

class Config extends Model
{
    protected $json = ['value'];
    protected $jsonAssoc = true;
}