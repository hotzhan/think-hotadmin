<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\common\model;

class UserRule extends Base
{

    public array $noDeletionIds = [1,2];
    public function getIsMenuTextAttr($value, $data)
    {
        return self::BOOLEN_TEXT[$data['is_menu']];
    }

    public function getIsShowTextAttr($value, $data)
    {
        return self::BOOLEN_TEXT[$data['is_show']];
    }
}