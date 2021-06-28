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
// | 插件基类 插件需要继承此类
// +----------------------------------------------------------------------
namespace addons;

use think\facade\Config;
use think\facade\View;

abstract class Addons
{
    // 当前插件标识
    protected $name;
    // 插件路径
    public $addon_path = '';
    // 插件配置作用域
    protected $configRange = 'addonconfig';
    // 插件信息作用域
    protected $infoRange = 'addoninfo';

    public function __construct()
    {
        $this->name = $this->getName();
        // 获取当前插件目录
        $this->addon_path = ADDON_PATH . $this->name . DIRECTORY_SEPARATOR;

        // 初始化视图模型
        $config = ['view_path' => $this->addon_path, 'cache_path' => app()->getRuntimePath() . 'temp' . DIRECTORY_SEPARATOR];
        $config = array_merge(Config::get('view'), $config);
        $this->view = clone View::engine('Think');
        $this->view->config($config);

        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }

    /**
     * @title 获取当前模块名
     * @return string
     */
    final public function getName()
    {
        $data = explode('\\', get_class($this));
        return strtolower(array_pop($data));
    }

    /**
     * 读取基础配置信息.
     *
     * @param string $name
     *
     * @return array
     */
    final public function getInfo($name = '')
    {
        if (empty($name)) {
            $name = $this->name;
        }
        $info = Config::get($name, $this->infoRange);
        if ($info) {
            return $info;
        }
        $info_file = $this->addon_path . 'info.ini';
        if (is_file($info_file)) {
            $info = parse_ini_file($info_file, true, INI_SCANNER_TYPED) ?: [];
            $info['url'] = addon_url($name);
        }
        Config::set([$name => $info], $this->infoRange);

        return $info ? $info : [];
    }

    /**
     * 获取插件的配置数组.
     *
     * @param string $name 可选模块名
     *
     * @return array
     */
    final public function getConfig($name = '')
    {
        if (empty($name)) {
            $name = $this->name;
        }
        $config = Config::get($name, $this->configRange);
        if ($config) {
            return $config;
        }
        $config_file = $this->addon_path . 'config.php';
        if (is_file($config_file)) {
            $temp_arr = include $config_file;
            foreach ($temp_arr as $key => $value) {
                $config[$value['name']] = $value['value'];
            }
            unset($temp_arr);
        }
        Config::set([$name => $config], $this->configRange);
        return $config;
    }

    /**
     * 获取完整配置列表.
     *
     * @param string $name
     *
     * @return array
     */
    final public function getFullConfig($name = '')
    {
        $fullConfigArr = [];
        if (empty($name)) {
            $name = $this->name;
        }
        $config_file = $this->addon_path . 'config.php';
        if (is_file($config_file)) {
            $fullConfigArr = include $config_file;
        }

        return $fullConfigArr;
    }

    /**
     * 设置配置数据.
     *
     * @param $name
     * @param array $value
     *
     * @return array
     */
    final public function setConfig($name = '', $value = [])
    {
        if (empty($name)) {
            $name = $this->name;
        }
        $config = $this->getConfig($name);
        $config = array_merge($config, $value);
        Config::set([$name => $config], $this->configRange);

        return $config;
    }

    /**
     * 设置插件信息数据.
     *
     * @param $name
     * @param array $value
     *
     * @return array
     */
    final public function setInfo($name = '', $value = [])
    {
        if (empty($name)) {
            $name = $this->name;
        }
        $info = $this->getInfo($name);
        $info = array_merge($info, $value);
        Config::set([$name => $info], $this->infoRange);

        return $info;
    }

    /**
     * 检查基础配置信息是否完整.
     *
     * @return bool
     */
    final public function checkInfo()
    {
        $info = $this->getInfo();
        $info_check_keys = ['name', 'title', 'description', 'author', 'version', 'status'];
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $info)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 获取模板引擎
     * @access public
     * @param string $type 模板引擎类型
     * @return $this
     */
    protected function engine($engine)
    {
        $this->view->engine($engine);
        return $this;
    }

    /**
     * 模板变量赋值
     * @param string|array $name  模板变量
     * @param mixed        $value 变量值
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        $this->view->assign([$name => $value]);
        return $this;
    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string $template 模板文件名或者内容
     * @param array  $vars     模板变量
     * @return string
     * @throws \Exception
     */
    protected function fetch($template = '', $vars = [])
    {
        return $this->view->fetch($template, $vars);
    }

    /**
     * 渲染内容输出
     * @param string $content 内容
     * @param array  $vars    模板变量
     * @return string
     */
    protected function display($content, $vars = [])
    {
        return $this->view->display($content, $vars);
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
