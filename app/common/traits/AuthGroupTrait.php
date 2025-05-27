<?php

namespace app\common\traits;

trait AuthGroupTrait
{
    public function getGroupChild( $data, int $pid = 0)
    {
        //初始化类目
        $array = [];
        //循环所有数据找$id的类目
        foreach ($data as $key => $datum)
        {
            //修改显示，模型里的text在getChild后无法传递，这里赋值一下
            $datum['status_text'] = $datum->status_text;

            //找到类目了
            if ($datum['pid'] == $pid) {
                //保存下来，然后继续找类目的类目
                $array[$key] = $datum;
                //先去掉自己，自己不可能是自己的儿孙
                unset($data[$key]);
                //递归找，并把找到的类目放到一个array的字段中
                $array[$key]['children'] = $this->getGroupChild($data, $datum['id']);
            }
        }
        return $array;
    }

    /**
     * 递归遍历分组，并将本分组以及子分组的ID全部保存到一个数组中
     * @param $data
     * @param $result 保存分组ID的数组
     * @param int $pid 父ID
     * @return void
     */
    public function getGroupChildIds($data, array &$result, int $pid = 0)
    {
        //循环所有数据找$id的类目
        foreach ($data as $key => $datum)
        {
            //找到类目了
            if ($datum['pid'] == $pid) {
                //保存下来，然后继续找类目的类目
                $result[] = $datum['id'];
                //先去掉自己，自己不可能是自己的儿孙
                unset($data[$key]);
                //递归找，并把找到的类目放到一个array的字段中
                $this->getGroupChildIds($data,$result, $datum['id']);
            }
        }
    }
}