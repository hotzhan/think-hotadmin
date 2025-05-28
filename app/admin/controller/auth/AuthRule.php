<?php
/**
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller\auth;

use app\admin\controller\Base;
use app\admin\model\AuthRule as AuthRuleModel;
use app\admin\model\AuthGroup as AuthGroupModel;
use think\facade\Cache;
use think\facade\View;

class AuthRule extends Base
{
    public function index()
    {
        $rules = $this->getRules(app()->http->getName(), $this->admin['group_id']);
        View::assign([
            'rules'=>json_encode($rules),
        ]);
        return View::fetch();
    }

    public function add(AuthRuleModel $model)
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            $res = $model->create($param);
            if($res)
            {
                //判断是否是菜单，如果是菜单，生成或者更新菜单缓存文件
                if($param['is_menu'] == 1)
                {
                    $this->deleteMenusCache(app()->http->getName());
                }
                //删除规则缓存文件
                $this->deleteRulesCache(app()->http->getName());

                return $this->success('添加成功', 200, [], 'index');
            }

            else
            {
                return $this->error('添加失败');
            }

        }
        else
        {
            $rules = $this->getRules(app()->http->getName(), $this->admin['group_id']);
            View::assign([
                'rules'=>json_encode($rules),
            ]);
            return View::fetch();
        }
    }

    public function edit(AuthRuleModel $model)
    {
        $param = $this->request->param();

        $id = $param['id'];
        $rule = $model->find($id);
        if($this->request->isPost())
        {
            $res = $rule->save($param);
            if($res)
            {
                //判断是否是菜单，如果是菜单，生成或者更新菜单缓存文件
                if($param['is_menu'] == 1)
                {
                    $this->deleteMenusCache(app()->http->getName());
                }
                //删除规则缓存文件
                $this->deleteRulesCache(app()->http->getName());

                return $this->success('修改成功',200, [], 'index');
            }
            else
            {
                return $this->error('修改失败', 500);
            }

        }
        else
        {
            $rules = $this->getRules(app()->http->getName(), $this->admin['group_id']);
            View::assign([
                'rule'=>$rule,
                'rules'=>json_encode($rules),
            ]);
            return View::fetch();
        }
    }

    public function del(AuthRuleModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];
        //判断规则下是否有子规则，其实还要判断角色组里的数据，角色组的单独处理不存在规则ID的情况
        $child = $model->where('parent_id', $id)->find();
        if($child)
            return $this->error('该规则下有数据，不可删除');
        $rule = $model->find($id);
        $res = $model->destroy($id);
        if($res)
        {
            //判断是否是菜单，如果是菜单，生成或者更新菜单缓存文件
            if($rule['is_menu'] == 1)
            {
                $this->deleteMenusCache(app()->http->getName());
            }
            //删除规则缓存文件
            $this->deleteRulesCache(app()->http->getName());

            return $this->success('删除成功', 200, [], URL_RELOAD);
        }
        else
        {
            return $this->error('删除失败');
        }

    }

    public function multiAdd(AuthRuleModel $model)
    {
        $param = $this->request->param();
        $ruleData = $model->find($param['id']);
        if($ruleData)
        {
            $data = [
                'parent_id' => $ruleData['id'],
                'icon' => 'far fa-circle',
                'sort_number' => 1000
            ];
            $rule = trim($ruleData['rule'], '/');
            $indexRes = true;
            $addRes = true;
            $editRes = true;
            $delRes = true;
            try {
                //查看
                $data['name'] = '查看';
                $data['rule'] = $rule . '/index';
                if(!$model->where('rule', '=', $data['rule'])->find())
                    $indexRes = $model->create($data);

                //添加
                $data['name'] = '添加';
                $data['rule'] = $rule . '/add';
                if(!$model->where('rule', '=', $data['rule'])->find())
                    $addRes = $model->create($data);

                //编辑
                $data['name'] = '编辑';
                $data['rule'] = $rule . '/edit';
                if(!$model->where('rule', '=', $data['rule'])->find())
                    $editRes = $model->create($data);

                //删除
                $data['name'] = '删除';
                $data['rule'] = $rule . '/del';
                if(!$model->where('rule', '=', $data['rule'])->find())
                    $delRes = $model->create($data);
            }
            catch (\Exception $exception)
            {
                return $this->error($exception->getMessage(), $exception->getCode());
            }


            if($indexRes && $addRes && $editRes && $delRes)
            {
                //删除规则缓存文件
                $this->deleteRulesCache(app()->http->getName());

                return $this->success('子规则批量添加成功', 200, [], 'index');
            }
            else
            {
                $tips = '';
                $tips .= $indexRes ? '' : '[查看]';
                $tips .= $addRes ? '' : '[添加]';
                $tips .= $editRes ? '' : '[编辑]';
                $tips .= $delRes ? '' : '[删除]';
                return $this->error('节点'. $tips .'添加失败');
            }
        }
        else
        {
            return $this->error('添加失败，未找到该规则');
        }
    }

    public function updateCache()
    {
        try {
            //删除规则缓存
            $this->deleteMenusCache(app()->http->getName());
            $this->deleteRulesCache(app()->http->getName());

            return $this->success('刷新', 200, [], URL_RELOAD);
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), $exception->getCode());
        }

    }

    public function rules(AuthRuleModel $model)
    {
        $rules = $this->getRules(app()->http->getName(), $this->admin['group_id']);
        /*if(!$rules)
        {
            //直接返回原始数据，递归放到前端操作，可以节省服务器性能和访问时间
            //$rules = $model->select();//这个转数组的时候模型中定义的字段text这种的就无效了
            //直接用filter可以改变数据模型
            $rules = $model->filter(function($rules){
                $rules->is_show_text = $rules->is_show == 1 ? '是' : '否';
                $rules->is_menu_text = $rules->is_menu == 1 ? '是' : '否';
            })->select();
        }*/

        $data = [
            'code' => 200,
            'msg' => '菜单信息',
            'data' => $rules
        ];
        return json($data);
    }

    public function menus()
    {
        $type = $this->request->param('type');
        $type = $type == 1 ? 1 : 0;
        $menus = $this->getMenus('admin', $this->admin['group_id']);

        $data = [
            'code' => 200,
            'msg' => '菜单信息',
            'data' => $menus
        ];
        return json($data);
    }
}