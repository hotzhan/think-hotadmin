<?php
/**
 * User: admin
 * Date: 2024/1/27
 */


namespace app\admin\model;

use think\model\concern\SoftDelete;
use think\model\relation\HasOne;
use think\model\relation\HasOneThrough;

class Admin extends Base
{
    protected $hidden = ['salt'];

    public array $noDeletionIds = [1,2];

    public function authGroupAccess():HasOne
    {
        return $this->hasOne(AuthGroupAccess::class, 'uid')->bind(['group_id']);
    }

    /**
     * 远程一对一关联
     * 该方法只能用在3个表都是一一对应的情况，当某个表出现一对多就无法使用了
     * @return HasOneThrough
     */
    public function authGroup(): HasOneThrough
    {
        /*
         * 参数说明：
         * model:关联模型类名
         * through:中间模型类名，起桥梁作用
         * foreignKey:当前模型主键关联的中间模型外键
         * throughKey:关联模型的主键，即第1个参数model对应的主键
         * localKey:当前模型主键
         * throughPk:中间模型(第2个参数)里对应关联模型(第1个参数)的键，该键和第4个参数键对应关联
         */
        //有问题，当多个重复的group_id时，只能显示1个
        return $this->hasOneThrough(AuthGroup::class,AuthGroupAccess::class, 'uid', 'id', 'id', 'group_id');
    }

    public function getStatusTextAttr($value, $data): string
    {
        return self::STATUS_TEXT[$data['status']];
    }
}