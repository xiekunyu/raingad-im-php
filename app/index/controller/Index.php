<?php
namespace app\index\controller;

use app\BaseController;
use app\enterprise\model\File;
use think\facade\View;

class Index extends BaseController
{

    public function index()
    {
        if (!file_exists(CONF_PATH . "install.lock")) {
            return redirect(url('index/install/index'));
        }
        if (request()->isMobile()) {
            return redirect("/h5");
        }
        return redirect("/index.html");
    }

    public function view()
    {
        return view::fetch();
    }

    //    头像生成
    public function avatar()
    {
        circleAvatar(input('str'), input('s') ?: 80, input('uid'));die;
    }

    // 文件下载
    public function download()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            throw new \think\Exception('请使用浏览器下载!',400);
        }
        $param = $this->request->param();
        $file_id = $param['file_id'] ?? 0;
        if (!$file_id) {
            throw new \think\Exception('参数错误', 502);
        }
        try {
            $file_id = decryptIds($file_id);
        } catch (\Exception $e) {
            throw new \think\Exception($e->getMessage(), 400);
        }
        $file = File::find($file_id);
        if (!$file) {
            throw new \think\Exception('该文件不存在!',404);
        }
        $file = $file->toArray();
        $url= getFileUrl($file['src']);
        return \utils\File::download($url, $file['name'] . '.' . $file['ext'], $file['size'], $file['ext']);
    }

}
