<?php
/**
 * User: hotadmin
 * Date: 2024/2/22
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\validate;

class Config extends Base
{
    protected $rule = [
        'name|名称' => 'require|token',
        'group_id|分组ID' => 'require',
        'code|代码' => 'require|unique:config,group_id^code',//多字段组合验证
    ];

    protected $message = [
        'code.unique' => '已存在该代码分组配置，请修改代码字符串文本'
    ];

    public function checkCode($value, $rule, $data, $field)
    {
        
    }
}