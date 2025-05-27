<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\common\model;

use think\model\relation\HasOne;

class User extends Base
{
    public function userGroupAccess():HasOne
    {
        return $this->hasOne(UserGroupAccess::class, 'uid')->bind(['group_id']);
    }
}