<?php

namespace app\common\validate;

class Article extends Base
{
    protected $rule = [
        'title|标题' => 'require|min:2',
        'content|内容' => 'require|min:2',
    ];
}