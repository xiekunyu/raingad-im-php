<?php
namespace app\controller;

use app\BaseController;
use app\model\{User};
use think\facade\View;
use think\facade\Session;
use think\facade\Db;
use think\facade\Cache;
use think\exception\ValidateException;
class Index extends BaseController
{

   public function index(){
    return view::fetch();
   }
}
