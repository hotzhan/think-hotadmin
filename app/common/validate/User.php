<?php
/**
 * User: hotadmin
 * Date: 2024/2/24
 * Site: https://www.hotadmin.cn
 */


namespace app\common\validate;

class User extends Base
{
    protected $rule = [
        'username|用户名' => 'require|min:4|unique:admin',
        'password|密码' => 'require|min:6',
        'new_password|新密码' => 'require|confirm:re_new_password|different:password',
        're_new_password|重复新密码' => 'require|confirm:new_password',
        'nickname|昵称' => 'require',
    ];

    protected $message = [
        'new_password.different' => '新密码和旧密码不能相同',
    ];

    protected $scene = [
        'add' => ['username', 'password', 'nickname'],
        'password' => ['password', 'new_password', 're_new_password'],
        'password2' => ['password'],
    ];
}