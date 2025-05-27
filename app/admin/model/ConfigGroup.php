<?php
/**
 * User: admin
 * Date: 2024/1/24
 */


namespace app\admin\model;

use think\model\relation\HasMany;

class ConfigGroup extends Base
{

    // 不可删除的数据ID，系统预留用
    public array $noDeletionIds = [1,2,3];

    //模型关联，关联Config
    public function config(): HasMany
    {
        return $this->hasMany(Config::class, 'group_id', 'id');
    }

    public function getConfigCode(string $code = '')
    {

    }

    //获取器：字段->自动生成配置文件
    public function getAutoCreateConfigTextAttr($value, $data): string
    {
        return self::BOOLEN_TEXT[$data['auto_create_config']];
    }
}