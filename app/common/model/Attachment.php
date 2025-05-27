<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\common\model;

class Attachment extends Base
{
    //protected $name = 'attachment';

    public function getFiletypeTextAttr($value, $data)
    {
        $text = ['image'=>'图片', 'video'=>'视频', 'file'=>'文件'];
        return $text[$data['filetype']];
    }
}
