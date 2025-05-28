<?php

namespace app\common\model;

use think\model\relation\HasOne;

class Article extends Base
{
    public function user():HasOne
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    public function category():HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function getStatusTextAttr($value, $data)
    {

        return self::STATUS_TEXT[$data['status']];
    }
}