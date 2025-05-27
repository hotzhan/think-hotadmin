<?php

namespace app\index\validate;

use think\Validate;

Class Login extends Validate
{
    protected $rule = [
        'username|用户名' => 'require|min:4|unique:user|token',
        'email|邮箱' => 'require|email|unique:user',
        'password|密码' => 'require|min:6',
        'password_confirm|确认密码' => 'require|min:6|confirm:password',
        'mobile|手机号' => 'require|mobile|unique:user',
        'mail_code|邮件证码' => 'require',
        'mail_token' => 'require',
        'sms_code|手机验证码' => 'require',
        'sms_token' => 'require',
        'terms|协议' => 'require',
        'captcha' => 'require',
        'new_password|新密码'=>'require|min:6|different:password',//会员中心 修改密码
        're_new_password|重复新密码'=>'require|confirm:new_password',//会员中心 修改密码
    ];

    protected $message = [
        'mobile' => '手机号已注册',
        'mail_token.require' => '邮箱验证码异常',
        'sms_token.require' => '手机验证码异常',
        'terms.require' => '请同意协议',
        'new_password.different'=>'新密码和旧密码不能相同',
    ];

    protected $scene = [
        'register' => ['username', 'email', 'password', 'password_confirm', 'mobile', 'terms'],
        'register_mail' => ['username', 'email', 'password', 'password_confirm', 'mobile', 'mail_code', 'mail_token', 'terms'],
        'register_sms' => ['username', 'email', 'password', 'password_confirm', 'mobile', 'sms_code', 'sms_token', 'terms'],
        'register_mail_sms' => ['username', 'email', 'password', 'password_confirm', 'mobile', 'mail_code', 'mail_token', 'sms_code', 'sms_token', 'terms'],
        'login' => ['username',  'password', 'captcha'],
        'regsms' => ['mobile'],
        'regmail' => ['email'],
        'modify_password'=>['password', 'new_password', 're_new_password'],
    ];
}