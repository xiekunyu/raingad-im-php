<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;
use app\manage\model\{Config};
use think\facade\Cache;
use thans\jwt\facade\JWTAuth;
/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 是否批量验证
     * @var bool
     */
    protected $userInfo = [];

        /**
     * 接收的post数据
     * @var bool
     */
    protected $postData = [];

    protected $uid = 0;

    protected $globalConfig = [];

    protected $chatSetting = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $this->userInfo=$this->request->userInfo;
        $this->uid=$this->userInfo['user_id'] ?? 0;
        $config=Config::getSystemInfo();
        if($config){
            $this->globalConfig = $config;
            $this->chatSetting = $config['chatInfo'] ?? [];
        }
        // 验证版本，如果不一致，就需要退出重新登陆
        $version =config('app.app_version');
        $oldVersion=Cache::get('app_version');
        if($version!=$oldVersion){
            Cache::set('app_version',$version);
            JWTAuth::refresh();
            Cache::delete('systemInfo');
        }
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    
    /**
     * 自动获取前端传递的分页数量
     * @param \think\Model|\think\model\relation\HasMany $model
     * @return \think\Paginator
     */
    protected function paginate($model)
    {
        $limit = $this->request->param('limit', 20);
        return $model->paginate($limit);
    }

}
