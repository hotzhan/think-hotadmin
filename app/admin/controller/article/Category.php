<?php

namespace app\admin\controller\article;

use app\admin\controller\Base;
use app\admin\traits\CategoryTrait;
use app\common\model\Category as CategoryModel;
use app\common\validate\Category as CategoryValidate;
use app\common\model\Article as ArticleModel;
use think\facade\View;

class Category extends Base
{
    use CategoryTrait;

    public function index(CategoryModel $model)
    {
//        $this->deleteCategoriesCache();
        $categories = $this->getCategories(app()->http->getName());
        View::assign([
            'categories'=>json_encode($categories),
        ]);
        return View::fetch();
    }

    public function add(CategoryModel $model)
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();

            $validate = new CategoryValidate();
            $validateResult = $validate->check($param);
            if(!$validateResult)
                return $this->error($validate->getError());

            $res = $model->create($param);
            if($res)
            {
                //删除菜单缓存，读取时会生成缓存
                if($param['is_menu'] == 1)
                {
                    $this->deleteCategoryMenusCache();
                }
                //删除栏目缓存，读取时会生成缓存
                $this->deleteCategoriesCache();

                return $this->success('添加成功', 200, [], 'index');
            }

            else
            {
                return $this->error('添加失败');
            }

        }
        else
        {
            $categories = $this->getCategories(app()->http->getName());
            View::assign([
                'categories'=>json_encode($categories),
            ]);
            return View::fetch();
        }
    }

    public function edit(CategoryModel $model)
    {
        $param = $this->request->param();

        $id = $param['id'];
        $category = $model->find($id);
        if($this->request->isPost())
        {
            $res = $category->save($param);
            if($res)
            {
                //删除菜单缓存，读取时会生成缓存
                if($param['is_menu'] == 1)
                {
                    $this->deleteCategoryMenusCache();
                }
                //删除栏目缓存，读取时会生成缓存
                $this->deleteCategoriesCache();

                return $this->success('修改成功',200, [], 'index');
            }
            else
            {
                return $this->error('修改失败', 500);
            }

        }
        else
        {
            $categories = $this->getCategories(app()->http->getName());
            View::assign([
                'category'=>$category,
                'categories'=>json_encode($categories),
            ]);
            return View::fetch();
        }
    }

    public function del(CategoryModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];

        //判断规则下是否有子栏目
        $child = $model->where('parent_id', $id)->find();
        if($child)
            return $this->error('该栏目下有子栏目，不可删除');

        //判断栏目下是否有文章
        $articleModel = new ArticleModel();
        $articles = $articleModel->where('category_id', $id)->find();
        if($articles)
            return $this->error('该栏目下有文章，不可删除');

        $category = $model->find($id);
        if($category)
        {
            $isMenu = $category['is_menu'] ?? 0;

            //栏目删除后，code字段加随机字符串，避免后面添加的栏目无法使用该缩写代码
            if($category)
            {
                $map = [
                    'status'=> 0,
                ];
                if(!str_contains($category['code'], '_delete_'))
                {

                    $map += [
                        'code'=> $category['code'].'_delete_'.md5(time()),
                    ];
                }
                $category->save($map);
            }
            $res = $model->destroy($id);
            if($res)
            {
                //删除菜单缓存文件
                if($isMenu == 1)
                {
                    $this->deleteCategoryMenusCache();
                }
                //删除规则缓存文件
                $this->deleteCategoriesCache();

                return $this->success('删除成功', 200, [], URL_RELOAD);
            }
            else
            {
                return $this->error('删除失败');
            }
        }
        else
        {
            return $this->error('删除失败，无法获取到该栏目信息，可能状态值为0');
        }
    }

    public function updateCache()
    {
        //删除规则缓存
        $this->deleteCategoryMenusCache();
        $this->deleteCategoriesCache();

        return $this->success('刷新', 200, [], URL_RELOAD);
    }
}