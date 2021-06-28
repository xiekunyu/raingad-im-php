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
// | 插件路由
// +----------------------------------------------------------------------
namespace think\addons;

use think\App;
use think\exception\HttpException;
use think\facade\Event;
use think\facade\Request;

class Route
{
    public static function execute($addon = null, $controller = null, $action = null)
    {
        $request = \request();
        // 是否自动转换控制器和操作名
        $convert = true;
        $filter = $convert ? 'strtolower' : 'trim';
        $addon = $addon ? trim(call_user_func($filter, $addon)) : '';
        $controller = $controller ? trim(call_user_func($filter, $controller)) : 'index';
        $action = $action ? trim(call_user_func($filter, $action)) : 'index';
        Event::trigger('addon_begin', $request);
        if (!empty($addon) && !empty($controller) && !empty($action)) {
            $info = get_addon_info($addon);
            if (!$info) {
                throw new HttpException(404, 'addon %s not found');
            }
            if (!$info['status']) {
                throw new HttpException(500, 'addon %s is disabled');
            }
            $path = app()->request->root();
            if ("" !== $path) {
                throw new HttpException(404, 'addon %s not found');
            }
            app()->http->setBind();
            // 设置当前请求的控制器、操作
            $request->setController($controller)->setAction($action);
            // 监听addon_module_init
            Event::trigger('addon_module_init', $request);
            $class = get_addon_class($addon, 'controller', $controller);
            if (!$class) {
                throw new HttpException(404, 'addon controller %s not found', $controller);
            }
            $instance = new $class(app());
            $vars = [];
            if (is_callable([$instance, $action])) {
                // 执行操作方法
                $call = [$instance, $action];
            } elseif (is_callable([$instance, '_empty'])) {
                // 空操作
                $call = [$instance, '_empty'];
                $vars = [$action];
            } else {
                // 操作不存在
                throw new HttpException(404, 'addon action %s not found', get_class($instance) . '->' . $action . '()');
            }
            Event::trigger('addon_action_begin', $call);
            return call_user_func_array($call, $vars);
        } else {
            abort(500, 'addon can not be empty');
        }
    }
}
