<?php

namespace app\common\validate;

class Category extends Base
{
    protected $rule = [
        'name|栏目名称' => 'require|min:2|unique:category',
        'code|栏目缩写代码' => 'require|min:2|unique:category',
    ];
}