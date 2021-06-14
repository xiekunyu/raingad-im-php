<?php
/**
 * lvzhe [a web admin based ThinkPHP5]
 */

namespace app\enterprise\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'account|帐号'      => 'require',
        'password|密码'     => 'require',
        'captcha|验证码'     => 'require|captcha',
        'oldpassword|旧密码' => 'require',
        'repassword|重复密码' => 'require',
    ];

    protected $scene = [
        'password' => ['password', 'oldpassword', 'repassword'],
        'login'    => ['account', 'password'],
    ];
}