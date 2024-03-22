<?php
/**
 * User: hotadmin
 * Date: 2024/2/6
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\model;

use think\model\relation\HasOne;

class AuthGroupAccess extends Base
{
    protected $name = 'auth_group_access';

    public array $noDeletionIds = [1,2];

    public function admin():HasOne
    {
        return $this->hasOne(Admin::class, 'uid');
    }

    public function authGroup(): HasOne
    {
        return $this->hasOne(AuthGroup::class, 'id', 'group_id');
    }
}