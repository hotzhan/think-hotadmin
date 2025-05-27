<?php
/**
 * User: admin
 * Date: 2024/1/22
 */


namespace app\admin\model;

use think\model\concern\SoftDelete;
use think\model\relation\BelongsTo;

class Config extends Base
{
    protected $name = 'config';
    protected $key = 'id';
    protected $json = ['content'];// 设置json类型字段 该字段保存写入时会将数组自动转换为json字符串

    // 不可删除的数据ID，系统预留用
    public array $noDeletionIds = [1,2,3,4,5,6,7];

    //模型关联，关联ConfigGroup
    public function configGroup(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'group_id', 'id');
    }
}