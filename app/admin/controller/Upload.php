<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller;

use app\common\traits\UploadTrait;

class Upload extends Base
{
    use UploadTrait;

    public function upload()
    {
        $param = $this->request->param();
        $param['uidStr'] = 'admin_id';
        $param['uid'] = $this->admin['id'];
        return $this->uploadFile($param);
    }

    public function del()
    {
        $param = $this->request->param();
        return $this->delFile($param);
    }
}