<?php

namespace app\admin\controller\article;

use app\admin\controller\Base;
use app\admin\traits\CategoryTrait;
use app\common\model\Article as ArticleModel;
use app\common\validate\Article as ArticleValidate;
use think\facade\View;
use think\response\Json;

class Article extends Base
{
    use CategoryTrait;

    public function index(ArticleModel $model)
    {
        $articles = $model->with(['user', 'category'])
            ->order('id desc')
            ->paginate($this->admin['pagesize']);

        $page = $articles->render();
        $total = $articles->total();

        View::assign([
            'articles' => $articles,
            'page' => $page,
            'total' => $total,
        ]);
        return View::fetch();
    }

    public function add(ArticleModel $model)
    {
        if(request()->isPost())
        {
            $params = request()->post();
            $params['uid'] = $this->admin['id'];

            $validate = new ArticleValidate();
            if(true !== $validate->check($params))
                return $this->error($validate->getError());

            //前端checkbox提交的可能是字符串on
            if(isset($params['is_recommend']) && $params['is_recommend'] == 'on')
                $params['is_recommend'] = 1;
            else
                $params['is_recommend'] = 0;
            if(isset($params['is_top']) && $params['is_top'] == 'on')
                $params['is_top'] = 1;
            else
                $params['is_top'] = 0;
            if(isset($params['is_swiper']) && $params['is_swiper'] == 'on')
                $params['is_swiper'] = 1;
            else
                $params['is_swiper'] = 0;

            $res = $model->create($params);
            if($res)
                return $this->success('添加成功', 200, [], 'index');
            else
                return $this->error('添加失败');
        }
        else
        {
            $categories = $this->getCategories(app()->http->getName());
            View::assign([
                'categories' => json_encode($categories),
            ]);
            return View::fetch('add');
        }
    }

    public function edit(ArticleModel $model)
    {
        $params = request()->param();
        if(!isset($params['id']))
            return $this->error('参数错误', 500, [], 'index');

        $article = $model->find($params['id']);
        if(request()->isPost())
        {
            $params = request()->post();
//            $params['uid'] = $this->admin['id'];

            $validate = new ArticleValidate();
            if(true !== $validate->check($params))
                return $this->error($validate->getError());

            //前端checkbox提交的可能是字符串on，取消勾选的话前端不会提交该字段，需要代码置为0
            if(isset($params['is_recommend']) && $params['is_recommend'] == 'on')
                $params['is_recommend'] = 1;
            else
                $params['is_recommend'] = 0;
            if(isset($params['is_top']) && $params['is_top'] == 'on')
                $params['is_top'] = 1;
            else
                $params['is_top'] = 0;
            if(isset($params['is_swiper']) && $params['is_swiper'] == 'on')
                $params['is_swiper'] = 1;
            else
                $params['is_swiper'] = 0;

            $res = $article->save($params);
            if($res)
                return $this->success('修改成功', 200, [], 'index');
            else
                return $this->error('修改失败');
        }
        else
        {
            $categories = $this->getCategories(app()->http->getName());
            View::assign([
                'categories' => json_encode($categories),
                'article' => $article,
            ]);
            return View::fetch();
        }
    }

    public function del(ArticleModel $model)
    {
        $params = request()->param();
        if(!isset($params['id']))
            return $this->error('参数错误');
        $id = $params['id'];
        $res = $model->destroy($id);
        if($res)
            return $this->success('删除成功', 200, [], URL_RELOAD);
        else
            return $this->error('删除失败');
    }

    public function disable(ArticleModel $model):Json
    {
        $param = $this->request->param();
        if(!isset($param['id']))
            return $this->error('请选择要操作的数据');
        $id = $param['id'];
        $res = $model->whereIn('id', $id)->update(['status'=>0]);
        if($res)
            return $this->success('禁用成功', 200, [], URL_RELOAD);
        else
            return $this->error('禁用失败');
    }

    public function enable(ArticleModel $model):Json
    {
        $param = $this->request->param();
        if(!isset($param['id']))
            return $this->error('请选择要操作的数据');
        $id = $param['id'];
        $res = $model->whereIn('id', $id)->update(['status'=>1]);
        if($res)
            return $this->success('启用成功', 200, [], URL_RELOAD);
        else
            return $this->error('启用失败');
    }

}