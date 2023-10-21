<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    'app_name' =>"Raingad-IM",
    'app_logo' =>"https://im.file.raingad.com/logo/logo.png",
    'app_version' =>"3.1.1",
    'app_release' =>"20231021",
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    'app_express'    =>    true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,
    'auto_multi_app' =>true,
     //用户token加密用的秘钥
     'aes_token_key' => env('AES_TOKEN_KEY', ''),
     //用户LOGIN加密用的秘钥
     'aes_login_key' => env('AES_LOGIN_KEY', ''),
     //用户chat加密用的秘钥
     'aes_chat_key' => env('AES_CHAT_KEY', ''),
];

