<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;

class Index extends BaseController
{

   public function index(){
      if (!file_exists(CONF_PATH . "install.lock")) {
         return redirect(url('index/install/index'));    
      }
      return redirect("/index.html");
      // return view::fetch();
   }

   public function view(){
      return view::fetch();
   }

   //    头像生成
   public function avatar(){
      circleAvatar(input('str'),input('s')?:80,input('uid'));die;
   }
}
