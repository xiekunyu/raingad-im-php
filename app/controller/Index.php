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
       echo "RAINGAD IM";
    // circleAvatar(input('str'),input('s')?:80,input('uid'));die;
    //    echo 123;
    //    echo root_path();
    //    dump(Session::getId());
    //    echo 4567;
   }
}
