<?php
/**
 * User: hotadmin
 * Date: 2024/2/22
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\validate;

class ConfigGroup extends Base
{
    protected $rule = [
        'module|作用模块' => 'require',
        'name|名称' => 'require|token',
        'code|代码' => 'require|unique:config_group'
    ];

    protected $message = [
        'code.unique' => '已存在该代码分组配置，请修改代码字符串文本'
    ];
}