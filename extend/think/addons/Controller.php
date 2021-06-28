<?php
// +----------------------------------------------------------------------
// | Yzncms [ 御宅男工作室 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://yzncms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 御宅男 <530765310@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 插件控制器
// +----------------------------------------------------------------------
namespace think\addons;

use app\common\controller\BaseController;
use think\App;
use think\facade\Config;
use think\facade\Event;
use think\facade\Lang;
use think\facade\View;
use think\helper\Str;

/**
 * 插件基类控制器.
 */
class Controller extends BaseController
{
    // 当前插件操作
    protected $addon = null;
    protected $controller = null;
    protected $action = null;
    // 当前template
    protected $template;

    /**
     * 架构函数.
     */
    public function __construct(App $app)
    {
        //移除HTML标签
        app()->request->filter('trim,strip_tags,htmlspecialchars');

        // 是否自动转换控制器和操作名
        $convert = Config::get('url_convert');

        $filter = $convert ? 'strtolower' : 'trim';
        // 处理路由参数
        $var = $param = app()->request->param();
        $addon = isset($var['addon']) ? $var['addon'] : '';
        $controller = isset($var['controller']) ? $var['controller'] : '';
        $action = isset($var['action']) ? $var['action'] : '';

        $this->addon = $addon ? call_user_func($filter, $addon) : '';
        $this->controller = $controller ? call_user_func($filter, $controller) : 'index';
        $this->action = $action ? call_user_func($filter, $action) : 'index';
        // 重置配置
        Config::set(['view_path' => ADDON_PATH . $this->addon . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR], 'view');
        // 父类的调用必须放在设置模板路径之后
        parent::__construct($app);
    }

    protected function _initialize()
    {
        // 渲染配置到视图中
        $config = get_addon_config($this->addon);
        $this->view->config(['view_path' => ADDON_PATH . $this->addon . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR]);
        $this->view->assign('config', $config);
    }

    /**
     * 加载模板输出.
     *
     * @param string $template 模板文件名
     * @param array  $vars     模板输出变量
     * @param array  $replace  模板替换
     * @param array  $config   模板参数
     *
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $controller = Str::studly($this->controller);
        if ('think' == strtolower(Config::get('template.type')) && $controller && 0 !== strpos($template, '/')) {
            $depr = Config::get('template.view_depr');
            $template = str_replace(['/', ':'], $depr, $template);
            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $this->action;
            } elseif (false === strpos($template, $depr)) {
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
            }
        }

        return View::fetch($template, $vars);
    }
}
