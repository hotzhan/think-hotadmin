<?php
/**
 * User: hotadmin
 * Date: 2024/2/2
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller\general;

use app\admin\controller\Base;
use app\common\model\Attachment as AttachmentModel;
use think\facade\Filesystem;
use think\facade\View;
use think\response\Json;

class Attachment extends Base
{
    public function index()
    {
        $attachments = AttachmentModel::order('id', 'desc')
            ->paginate($this->admin['pagesize']);
        $total = $attachments->total();
        $page = $attachments->render();
        View::assign([
           'attachments' => $attachments,
            'total' => $total,
            'page' => $page
        ]);

        return View::fetch();
    }

    public function del(AttachmentModel $model):Json
    {
        $param = $this->request->param();
        if(!isset($param['id']))
            return $this->error('请选择要操作的数据');
        $id = $param['id'];

        $unDeleteAble = $model->inNoDeletionIds($id);
        $unDelIds = "";
        if(!is_array($unDeleteAble))
        {
            if($unDeleteAble)
                return $this->error('不可删除记录，系统保留用');
        }
        else
        {
            //可删除的id
            $id = $unDeleteAble['diff'];
            $unDelIds = implode(',', $unDeleteAble['intersect']);
        }

        $files = $model->whereIn('id', $id)->select();

        $res = $model->destroy(static function ($query) use ($id){
            /** @var \think\db\Query $query */
            $query->whereIn('id', $id);
        });
        if($res)
        {
            //删除对应文件
            foreach ($files as $file)
            {
                unlink(app()->getRootPath() . 'public' . $file->url);
            }

            if($unDelIds != '')
                return $this->error('删除失败，部分未删除ID:' . $unDelIds, 500, [], URL_RELOAD);
            else
                return $this->success('删除成功', 200, [], URL_RELOAD);
        }
        else
        {
            return $this->error('删除失败');
        }
    }
}