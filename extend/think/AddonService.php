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
// | 插件服务类
// +----------------------------------------------------------------------
namespace think;

use app\admin\model\Addons as AddonsModel;
use app\common\model\Cache as CacheModel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;
use util\File;
use util\Sql;
use ZipArchive;

class AddonService
{
    /**
     * 安装插件.
     *
     * @param string $name   插件名称
     * @param bool   $force  是否覆盖
     * @param array  $extend 扩展参数
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function install($name, $force = false, $extend = [])
    {
        try {
            // 检查插件是否完整
            self::check($name);
            if (!$force) {
                self::noconflict($name);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        /*if (!$name || (is_dir(ADDON_PATH . $name) && !$force)) {
        throw new Exception('插件已存在！');
        }*/
        foreach (self::getCheckDirs() as $k => $dir) {
            if (is_dir(ADDON_PATH . $name . DS . $dir)) {
                File::copy_dir(ADDON_PATH . $name . DS . $dir, app()->getRootPath() . $dir);
            }
        }
        //前台模板
        $installdir = ADDON_PATH . "{$name}" . DS . "install" . DS;
        if (is_dir($installdir . "template" . DS)) {
            //拷贝模板到前台模板目录中去
            File::copy_dir($installdir . "template" . DS, TEMPLATE_PATH . 'default' . DS);
        }
        //静态资源文件
        if (file_exists(ADDON_PATH . $name . DS . "install" . DS . "public" . DS)) {
            //拷贝模板到前台模板目录中去
            File::copy_dir(ADDON_PATH . $name . DS . "install" . DS . "public" . DS, app()->getRootPath() . 'public' . DS . 'static' . DS . 'addons' . DS . strtolower($name) . '/');
        }

        try {
            // 默认启用该插件
            $info = get_addon_info($name);
            if (!$info['status']) {
                $info['status'] = 1;
                set_addon_info($name, $info);
            }
            // 执行安装脚本
            $class = get_addon_class($name);
            if (class_exists($class)) {
                $addon = new $class();
                $addon->install();
                //缓存
                if (isset($addon->cache) && is_array($addon->cache)) {
                    self::installAddonCache($addon->cache, $name);
                }
            }
            self::runSQL($name);
            AddonsModel::create($info);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        // 刷新
        self::refresh();
        return true;

    }

    /**
     * 卸载插件.
     *
     * @param string $name
     * @param bool   $force 是否强制卸载
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function uninstall($name, $force = false)
    {
        if (!$name || !is_dir(ADDON_PATH . $name)) {
            throw new Exception('插件不存在！');
        }
        // 移除插件全局资源文件
        if ($force) {
            $list = self::getGlobalFiles($name);
            foreach ($list as $k => $v) {
                @unlink(app()->getRootPath() . $v);
            }
        }
        //删除模块前台模板
        if (is_dir(TEMPLATE_PATH . 'default' . DS . $name . DS)) {
            File::del_dir(TEMPLATE_PATH . 'default' . DS . $name . DS);
        }
        //静态资源移除
        if (is_dir(app()->getRootPath() . 'public' . DS . 'static' . DS . 'addons' . DS . strtolower($name) . DS)) {
            File::del_dir(app()->getRootPath() . 'public' . DS . 'static' . DS . 'addons' . DS . strtolower($name) . DS);
        }
        // 执行卸载脚本
        try {
            // 默认禁用该插件
            $info = get_addon_info($name);
            if ($info['status']) {
                $info['status'] = 0;
                set_addon_info($name, $info);
            }
            $class = get_addon_class($name);
            if (class_exists($class)) {
                $addon = new $class();
                $addon->uninstall();
                //缓存
                if (isset($addon->cache) && is_array($addon->cache)) {
                    CacheModel::where(['module' => $name, 'system' => 0])->delete();
                }
            };
            self::runSQL($name, 'uninstall');
            AddonsModel::where('name', $name)->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        // 刷新
        self::refresh();
        return true;
    }

    /**
     * 启用.
     *
     * @param string $name  插件名称
     * @param bool   $force 是否强制覆盖
     *
     * @return bool
     */
    public static function enable($name, $force = false)
    {
        if (!$name || !is_dir(ADDON_PATH . $name)) {
            throw new Exception('插件不存在！');
        }

        $info = get_addon_info($name);
        $info['status'] = 1;
        unset($info['url']);
        set_addon_info($name, $info);

        //执行启用脚本
        try {
            AddonsModel::update(['status' => 1], ['name' => $name]);
            $class = get_addon_class($name);
            if (class_exists($class)) {
                $addon = new $class();
                if (method_exists($class, 'enable')) {
                    $addon->enable();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        // 刷新
        self::refresh();

        return true;
    }

    /**
     * 禁用.
     *
     * @param string $name  插件名称
     * @param bool   $force 是否强制禁用
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function disable($name, $force = false)
    {
        if (!$name || !is_dir(ADDON_PATH . $name)) {
            throw new Exception('插件不存在！');
        }

        $info = get_addon_info($name);
        $info['status'] = 0;
        unset($info['url']);
        set_addon_info($name, $info);

        // 执行禁用脚本
        try {
            AddonsModel::update(['status' => 0], ['name' => $name]);
            $class = get_addon_class($name);
            if (class_exists($class)) {
                $addon = new $class();

                if (method_exists($class, 'disable')) {
                    $addon->disable();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // 刷新
        self::refresh();

        return true;
    }

    /**
     * 刷新插件缓存文件.
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function refresh()
    {
        $file = app()->getRootPath() . 'config' . DS . 'addons.php';

        $config = get_addon_autoload_config(true);
        if ($config['autoload']) {
            return;
        }

        if (!\util\File::is_really_writable($file)) {
            throw new Exception('addons.php文件没有写入权限');
        }

        if ($handle = fopen($file, 'w')) {
            fwrite($handle, "<?php\n\n" . 'return ' . var_export($config, true) . ';');
            fclose($handle);
        } else {
            throw new Exception('文件没有写入权限');
        }

        return true;
    }

    /**
     * 解压插件.
     *
     * @param string $name 插件名称
     *
     * @throws Exception
     *
     * @return string
     */
    public static function unzip($name)
    {
        $file = app()->getRootPath() . 'runtime' . DS . 'addons' . DS . $name . '.zip';
        $dir = ADDON_PATH . $name . DS;
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($file) !== true) {
                throw new Exception('Unable to open the zip file');
            }
            if (!$zip->extractTo($dir)) {
                $zip->close();

                throw new Exception('Unable to extract the file');
            }
            $zip->close();

            return $dir;
        }

        throw new Exception('无法执行解压操作，请确保ZipArchive安装正确');
    }

    /**
     * 注册插件缓存
     * @return boolean
     */
    public static function installAddonCache(array $cache, $name)
    {
        $data = array();
        foreach ($cache as $key => $rs) {
            $add = array(
                'key' => $key,
                'name' => $rs['name'],
                'module' => isset($rs['module']) ? $rs['module'] : $name,
                'model' => $rs['model'],
                'action' => $rs['action'],
                //'param' => isset($rs['param']) ? $rs['param'] : '',
                'system' => 0,
            );
            CacheModel::create($add);
        }
        return true;
    }

    /**
     * 执行安装数据库脚本
     * @param type $name 模块名(目录名)
     * @return boolean
     */
    public static function runSQL($name = '', $Dir = 'install')
    {
        $sql_file = ADDON_PATH . "{$name}" . DS . "{$Dir}" . DS . "{$Dir}.sql";
        if (file_exists($sql_file)) {
            $sql_statement = Sql::getSqlFromFile($sql_file);
            if (!empty($sql_statement)) {
                foreach ($sql_statement as $value) {
                    try {
                        Db::execute($value);
                    } catch (\Exception $e) {
                        throw new Exception('导入SQL失败，请检查{$name}.sql的语句是否正确');
                    }
                }
            }
        }
        return true;
    }

    /**
     * 是否有冲突
     *
     * @param   string $name 插件名称
     * @return  boolean
     * @throws  AddonException
     */
    public static function noconflict($name)
    {
        // 检测冲突文件
        $list = self::getGlobalFiles($name, true);
        if ($list) {
            //发现冲突文件，抛出异常
            throw new Exception("发现冲突文件");
        }
        return true;
    }

    /**
     * 获取插件在全局的文件
     *
     * @param   string $name 插件名称
     * @return  array
     */
    public static function getGlobalFiles($name, $onlyconflict = false)
    {
        $list = [];
        $addonDir = ADDON_PATH . $name . DS;
        // 扫描插件目录是否有覆盖的文件
        foreach (self::getCheckDirs() as $k => $dir) {
            $checkDir = app()->getRootPath() . DS . $dir . DS;
            if (!is_dir($checkDir)) {
                continue;
            }

            //检测到存在插件外目录
            if (is_dir($addonDir . $dir)) {
                //匹配出所有的文件
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($addonDir . $dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $filePath = $fileinfo->getPathName();
                        $path = str_replace($addonDir, '', $filePath);
                        if ($onlyconflict) {
                            $destPath = app()->getRootPath() . $path;
                            if (is_file($destPath)) {
                                if (filesize($filePath) != filesize($destPath) || md5_file($filePath) != md5_file($destPath)) {
                                    $list[] = $path;
                                }
                            }
                        } else {
                            $list[] = $path;
                        }
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 获取检测的全局文件夹目录
     * @return  array
     */
    protected static function getCheckDirs()
    {
        return [
            'app',
            'public',
        ];
    }

    /**
     * 检测插件是否完整.
     *
     * @param string $name 插件名称
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function check($name)
    {
        if (!$name || !is_dir(ADDON_PATH . $name)) {
            throw new Exception('插件不存在！');
        }
        $addonClass = get_addon_class($name);
        if (!$addonClass) {
            throw new Exception('插件主启动程序不存在');
        }
        $addon = new $addonClass();
        if (!$addon->checkInfo()) {
            throw new Exception('配置文件不完整');
        }
        return true;
    }

}
