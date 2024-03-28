<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\common\model;

use think\model\relation\HasOne;

class UserGroup extends Base
{

    public array $noDeletionIds = [1];
    public function getStatusTextAttr($value, $data)
    {
        return self::STATUS_TEXT[$data['status']];
    }
}