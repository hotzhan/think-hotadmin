<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\index\controller;

use app\common\traits\UploadTrait;

class Upload extends Base
{
    use UploadTrait;

    public function upload()
    {
        $param = $this->request->param();
        $param['uidStr'] = 'user_id';
        $param['uid'] = $this->user['id'];
        return $this->uploadFile($param);
    }

    public function del()
    {
        $param = $this->request->param();
        return $this->delFile($param);
    }
}