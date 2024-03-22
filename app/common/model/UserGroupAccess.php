<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\common\model;

use think\model\relation\HasOne;

class UserGroupAccess extends Base
{

    public function user():HasOne
    {
        return $this->hasOne(User::class, 'uid');
    }

    public function userGroup():HasOne
    {
        return $this->hasOne(UserGroup::class, 'id', 'group_id');
    }
}