<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller;

use app\common\traits\UploadTrait;
use think\response\Json;

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

    /**
     * 编辑器图片上传，可用于summernote/ckeditor等编辑器上传
     * @return \think\response\Json
     */
    public function editor(): Json
    {
        $param = $this->request->param();
        $param['uidStr'] = 'admin_id';
        $param['uid'] = $this->admin['id'];
        try {
            return $this->editorUploadFile('upload', $param); // upload是前端文件对应的字段
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}