<?php
/**
 * User: hotadmin
 * Date: 2024/2/3
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\model;

class AuthRule extends Base
{
    protected $name = 'auth_rule';

    public array $noDeletionIds = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27];

    public function getIsMenuTextAttr($value, $data): string
    {
        return self::BOOLEN_TEXT[$data['is_menu']];
    }

    public function getIsShowTextAttr($value, $data): string
    {
        return self::BOOLEN_TEXT[$data['is_show']];
    }

}