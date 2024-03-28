<?php
/**
 * User: hotadmin
 * Date: 2024/2/5
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\model;

use think\model\relation\HasOne;

class AuthGroup extends Base
{
    protected $name = 'auth_group';

    public array $noDeletionIds = [1,2];

    /*
    public function authGroupAccess():HasOne
    {
        return $this->hasOne(AuthGroupAccess::class, 'group_id');
    }
    */
    public function getStatusTextAttr($value, $data): string
    {
        return self::STATUS_TEXT[$data['status']];
    }
}