<?php
/**
 * User: hotadmin
 * Date: 2024/3/21
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\validate;

class UserRule extends Base
{
    protected $rule = [
        'name|名称' => 'require',
        'rule|url' => 'require|unique:user_rule',
    ];
}