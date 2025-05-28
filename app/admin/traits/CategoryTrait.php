<?php
/**
 * 文章栏目递归获取
 * 参考AuthRuleTrait代码
 */

namespace app\admin\traits;

use app\common\model\Category as CategoryModel;
use app\facade\Url;
use think\facade\Cache;


trait CategoryTrait
{
    private string $categoryCacheKey = 'category_list' ;
    private string $menusCacheKey = 'category_menus' ;

    /**
     * 菜单或者分类无限分级递归
     * @param $data
     * @param int $pid 父id
     * @param int $urlType 0默认数据， 1会根据应用名补全url
     * @return array
     */
    public function getCategoryChild( $data, int $pid = 0, int $urlType = 0, string $appName=''): array
    {
        //初始化类目
        $array = [];
        //循环所有数据找$id的类目
        foreach ($data as $key => $datum)
        {
            //根据应用名补全url
            if($urlType == 1)
            {
                if($appName != '')
                    $datum['rule'] = Url::appUrl($appName, $datum['rule'], [], false);//指定appName
                else
                    $datum['rule'] = _url($datum['rule']);//根据当前的appName
            }

            //修改显示，模型里的text在getChild后无法传递，这里赋值一下
//            $datum['is_menu_text'] = $datum->is_menu_text; // 是否菜单
//            $datum['is_show_text'] = $datum->is_show_text; // 是否显示

            //找到类目了
            if ($datum['parent_id'] == $pid) {
                //保存下来，然后继续找类目的类目
                $array[$key] = $datum;
                //先去掉自己，自己不可能是自己的儿孙
                unset($data[$key]);
                //递归找，并把找到的类目放到一个array的字段中
                $array[$key]['children'] = $this->getCategoryChild($data, $datum['id'], $urlType, $appName);
            }
        }
        return $array;
    }

    /**
     * 获取全部栏目含子栏目
     * @param string $appName 应用，比如admin、index、api等
     * @param bool $force 强制从数据库中获取数据并缓存
     * @return array
     */
    public function getCategories(string $appName, bool $force = false): array
    {

        $categories = Cache::get($this->categoryCacheKey);
        if(!$categories || $force)
        {
            $model = new CategoryModel();
            $categories = $model->where('status', 1)
                ->order('sort_number', 'asc')
                ->select();
            //dump($categories);
            $categories = $this->getCategoryChild( $categories, 0, 0, $appName);
            Cache::set($this->categoryCacheKey, $categories);
        }

        //halt($categories);
        return $categories;
    }

    /**
     * 根据栏目获取菜单，一般在前台菜单导航中使用
     * @param string $appName
     * @param bool $force
     * @return array
     */
    public function getCategoryMenus(string $appName, bool $force = false): array
    {
        $menus = Cache::get($this->menusCacheKey);
        if (!$menus || $force) {
            $model = new CategoryModel();
            $menus = $model->where([
                ['is_menu', '=', 1],
                ['is_show', '=', 1],
                ['status', '=', 1],
            ])->order('sort_number', 'asc')->select();
            //dump($menus);
            $menus = $this->getCategoryChild($menus, 0, 1, $appName);
            Cache::set($this->menusCacheKey, $menus);
        }
        return $menus;
    }

    public function deleteCategoriesCache(): void
    {
        Cache::delete($this->categoryCacheKey);
    }

    public function deleteCategoryMenusCache(): void
    {
        Cache::delete($this->menusCacheKey);
    }
}