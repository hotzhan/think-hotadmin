<?php
/**
 * User: admin
 * Date: 2024/1/24
 */


namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Base extends Model
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    public const BOOLEN_TEXT = [0=>'否', 1=>'是'];
    public const STATUS_TEXT = [0=>'禁用', 1=>'正常'];

    // 不可删除的数据ID，系统预留用
    public array $noDeletionIds = [];

    /**
     * 是否不可删除ID
     * @param $id
     * @return bool
     */
    public function inNoDeletionIds(int|array $id):bool|array
    {
        if (count($this->noDeletionIds) > 0) {
            if(is_array($id))
            {
                $intersect = array_intersect($id, $this->noDeletionIds);
                if(!empty($intersect))
                {
                    $diff = array_diff($id, $this->noDeletionIds);
                    if(empty($diff))
                        return true;
                    else
                        return ['intersect'=>$intersect, 'diff'=>$diff];
                }
            }
            else if(in_array($id, $this->noDeletionIds))
            {
                return true;
            }
        }
        return false;
    }
}