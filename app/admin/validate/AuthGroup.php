<?php
/**
 * User: hotadmin
 * Date: 2024/2/6
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\validate;

class AuthGroup extends Base
{
    protected $rule = [
        'name|名称'=>'require|token',
    ];

    protected $message = [
        'name.require' => '名称不能为空',
    ];

    protected $sence = [
        'edit' => ['name'],
    ];
}