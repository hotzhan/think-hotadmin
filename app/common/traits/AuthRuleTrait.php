<?php

namespace app\common\traits;

use app\admin\model\AuthGroup as AuthGroupModel;
use app\admin\model\AuthRule as AuthRuleModel;
use app\common\model\UserRule as UserRuleModel;
use app\facade\Auth;
use app\facade\Url;
use think\facade\Cache;

trait AuthRuleTrait
{
    /**
     * 菜单或者分类无限分级递归
     * @param $data
     * @param int $pid 父id
     * @param int $urlType 0默认数据， 1会根据应用名补全url
     * @return array
     */
    public function getChild( $data, int $uid = 0, int $pid = 0, int $urlType = 0, string $appName='')
    {
        //初始化类目
        $array = [];
        //循环所有数据找$id的类目
        foreach ($data as $key => $datum)
        {
            //规则权限校验
            if($uid != 0)
            {
                if(!Auth::check($datum['rule'], $uid))
                    continue;
            }
            //根据应用名补全url
            if($urlType == 1)
            {
                if($appName != '')
                    $datum['rule'] = Url::appUrl($appName, $datum['rule'], [], false);//指定appName
                else
                    $datum['rule'] = _url($datum['rule']);//根据当前的appName
            }

            //修改显示，模型里的text在getChild后无法传递，这里赋值一下
            $datum['is_menu_text'] = $datum->is_menu_text;
            $datum['is_show_text'] = $datum->is_show_text;

            //找到类目了
            if ($datum['parent_id'] == $pid) {
                //保存下来，然后继续找类目的类目
                $array[$key] = $datum;
                //先去掉自己，自己不可能是自己的儿孙
                unset($data[$key]);
                //递归找，并把找到的类目放到一个array的字段中
                $array[$key]['children'] = $this->getChild($data, $uid, $datum['id'], $urlType, $appName);
            }
        }
        return $array;
    }

    public function getChildByGid( $data, int $gid = 0, int $pid = 0, int $urlType = 0, string $appName=''):array
    {
        //初始化类目
        $array = [];
        //循环所有数据找$id的类目
        foreach ($data as $key => $datum)
        {
            //规则权限校验
            if($gid != 0)
            {
                //Auth
                if(!Auth::checkRuleIdByGroupId($datum['rule'], $gid))
                {
                    continue;
                }

            }
            //根据应用名补全url
            if($urlType == 1)
            {
                if($appName != '')
                    $datum['url'] = Url::appUrl($appName, $datum['rule'], []);//指定appName
                else
                    $datum['url'] = _url($datum['rule']);//根据当前的appName
            }

            //修改显示，模型里的text在getChild后无法传递，这里赋值一下
            $datum['is_menu_text'] = $datum->is_menu_text;
            $datum['is_show_text'] = $datum->is_show_text;

            //找到类目了
            if ($datum['parent_id'] == $pid) {
                //保存下来，然后继续找类目的类目
                $array[$key] = $datum;
                //先去掉自己，自己不可能是自己的儿孙
                unset($data[$key]);
                //递归找，并把找到的类目放到一个array的字段中
                $array[$key]['children'] = $this->getChildByGid($data, $gid, $datum['id'], $urlType, $appName);
            }
        }
        return $array;
    }

    //根据授权分组保存菜单缓存
    public function getMenus(string $app, int $groupId=0, bool $force = false): array
    {
        //$groupId = $user['group_id'] ?? 0;
        $menusCacheKey = $app . '_menus_gid_' . $groupId;
        $menus = Cache::get($menusCacheKey);
        if(!$menus || $force)
        {
            switch($app)
            {
                case "admin":
                    $model = new AuthRuleModel();
                    $pid = 0;
                    break;
                default:
                    $pid = 1;
                    $model = new UserRuleModel();
            }
            $menus = $model->where([
                ['is_menu','=',1],
                ['is_show','=',1]
            ])->order('sort_number', 'asc')->select();
            //dump($menus);
            $menus = $this->getChildByGid( $menus, $groupId, $pid, 1, $app);
            Cache::set($menusCacheKey, $menus);
        }

        //halt($menus);
        return $menus;
    }

    //根据授权分组保存菜单缓存
    public function getRules(string $app, int $groupId = 0, bool $force = false): array
    {
        //$groupId = $user['group_id'] ?? 0;
        $rulesCacheKey = $app . '_rules_gid_' . $groupId;
        $rules = Cache::get($rulesCacheKey);
        if(!$rules || $force)
        {
            switch($app)
            {
                case "admin":
                    $model = new AuthRuleModel();
                    break;
                default:
                    $model = new UserRuleModel();
            }
            $rules = $model->order('sort_number', 'asc')->select();
            //dump($menus);
            $rules = $this->getChildByGid( $rules, $groupId, 0, 1, $app);
            Cache::set($rulesCacheKey, $rules);
        }

        //halt($menus);
        return $rules;
    }

    public function deleteMenusCache(string $app)
    {
        //删除每个分组菜单缓存，等访问后自动更新缓存
        $menusCacheKeyPrefix = $app . '_menus_gid_';
        Cache::delete($menusCacheKeyPrefix . '0');

        $groups = AuthGroupModel::where('status', 1)->select();
        foreach ($groups as $group)
        {
            Cache::delete($menusCacheKeyPrefix . $group['id']);
        }
    }

    public function deleteRulesCache(string $app)
    {
        //删除每个分组菜单缓存，等访问后自动更新缓存
        $rulesCacheKeyPrefix = $app . '_rules_gid_';
        Cache::delete($rulesCacheKeyPrefix . '0');

        $groups = AuthGroupModel::where('status', 1)->select();
        foreach ($groups as $group)
        {
            Cache::delete($rulesCacheKeyPrefix . $group['id']);
        }
    }

    protected function getRuleName(string $app):string
    {
        $ruleNameCacheKey = $app . "_rule_name";
        $ruleNameCache = Cache::get($ruleNameCacheKey);
        //如果没有缓存，则从数据库中获取数据
        if(!$ruleNameCache)
        {
            switch($app)
            {
                case "admin":
                    $model = new AuthRuleModel();
                    break;
                default:
                    $model = new UserRuleModel();
            }
            $rules = $model->order('sort_number', 'asc')->select();

            //设置 rule=>id 的缓存，用于面包屑导航直接获取对应链接的规则菜单id，可以避免每次去查询数据库
            $ruleNameCache = [];
            foreach ($rules as $rule)
            {
                $ruleNameCache[$rule['rule']] = $rule['name'];
            }
            //保存到缓存
            Cache::set($ruleNameCacheKey, $ruleNameCache);

        }
        //当前访问的url
        $url = $this->controller . '/' . $this->action;

        //当前访问的url对应的菜单规则ID
        if(!isset($ruleNameCache[$url]))
        {
            //如果菜单规则在填写的时候加了模块名 比如：admin/user/index 或 index/user/index
            $url = _url($url, [], false);
            if(str_starts_with($url, "/"))
            {
                $url = ltrim($url, '/');
            }

            if(!isset($ruleNameCache[$url]))
            {
                $url = $this->controller;
            }
        }

        //判断控制器有做分层处理的链接
        $count = substr_count($url, '/');
        if($count >= 2)
        {
            $url = $this->controller;
        }
        //dump($url);
        //halt($ruleNameCache);

        //如果当前url未找到对应的规则id
        if(!isset($ruleNameCache[$url]))
            return '';

        $ruleName = $ruleNameCache[$url];
        return $ruleName;
    }

    protected function getBreadCrumb(string $app): string
    {
        //非父子节点的菜单缓存，就是直接数据读取后未进行递归父子节点保存的缓存
        $menusNormalCacheKey = $app . "_menus_normal";
        $menusNormal = Cache::get($menusNormalCacheKey);

        //非父子节点的菜单缓存的 rule=>id ，可根据当前url快速获取规则rule的id
        $menusNormalRuleIdCacheKey = $app . "_menus_normal_rule_id";
        $menusNormalRuleId = Cache::get($menusNormalRuleIdCacheKey);

        //如果没有缓存，则从数据库中获取数据
        if(!$menusNormal || !$menusNormalRuleId)
        {
            switch($app)
            {
                case "admin":
                    $model = new AuthRuleModel();
                    break;
                default:
                    $model = new UserRuleModel();
            }
            $menus = $model->where([
                //['is_menu','=',1],
                //['is_show','=',1]
            ])->order('sort_number', 'asc')->select();
            if(!$menusNormal)
            {
                //保存到缓存
                Cache::set($menusNormalCacheKey, $menus->toArray());
            }

            //设置 rule=>id 的缓存，用于面包屑导航直接获取对应链接的规则菜单id，可以避免每次去查询数据库
            if(!$menusNormalRuleId)
            {
                $menusNormalRuleId = [];
                foreach ($menus as $menu)
                {
                    $menusNormalRuleId[$menu['rule']] = $menu['id'];
                }
                //保存到缓存
                Cache::set($menusNormalRuleIdCacheKey, $menusNormalRuleId);
            }

        }
        //当前访问的url
        $url = $this->controller . '/' . $this->action;

        //当前访问的url对应的菜单规则ID
        if(!isset($menusNormalRuleId[$url]))
        {
            //如果菜单规则在填写的时候加了模块名 比如：admin/user/index 或 index/user/index
            $url = _url($url, [], false);
            if(str_starts_with($url, "/"))
            {
                $url = ltrim($url, '/');
            }

            if(!isset($menusNormalRuleId[$url]))
            {
                $url = $this->controller;
            }
        }

        //如果当前url未找到对应的规则id
        if(!isset($menusNormalRuleId[$url]))
            return '';

        $ruleId = $menusNormalRuleId[$url];
        return $this->getBreadCrumbHtml($menusNormal, $ruleId);
    }

    /**
     * 根据菜单数组和当前菜单规则ID生成面包屑导航html
     * @param $data 菜单数组
     * @param $id 当前菜单规则ID
     * @param string $currentNav 当前面包屑导航html
     * @return string
     */
    protected function getBreadCrumbHtml($menus, $id, string $currentNav = ''): string
    {
        $breadcrumb = $currentNav;
        $menus = $menus ?? [];
        foreach ($menus as $key=>$value) {
            if ($value['id'] == $id) {
                /*if($currentNav == '')
                    $html = '<li class="breadcrumb-item acvite">' . $value['name'] . '</li>';
                else
                {
                    //菜单和显示的才可以点击链接
                    if($value['is_menu'] && $value['is_show'])
                        $url = _url($value['rule']);
                    else
                        $url = "#";
                    $html = '<li class="breadcrumb-item"><a href="'. $url .'">' . $value['name'] . '</a></li>';
                }*/

                $html = '<li class="breadcrumb-item">' . $value['name'] . '</li>';
                $breadcrumb = $html . $currentNav;

                if ($value['parent_id'] > 1) {
                    unset($menus[$key]);
                    return $this->getBreadCrumbHtml($menus, $value['parent_id'], $breadcrumb);
                }

            }
        }
        return $breadcrumb;
    }

    public function getBreadCrumb_bak($data, $id)
    {
        $name = "";
        foreach ($data as $key=>$datum)
        {
            if($datum['is_menu'] == 1)
            {
                if($datum['id'] == $id)
                {
                    $name = $datum['name'] . ($name == "" ? "" : "/".$name);
                    $pname = "";
                    if($datum['parent_id'] > 0)
                    {
                        unset($data[$key]);
                        $pname = $this->getBreadCrumb($data,  $datum['parent_id']);
                    }
                    $name = ($pname == "" ? "" : $pname . "/") . $name;
                }
            }

        }
        return $name;
    }


}