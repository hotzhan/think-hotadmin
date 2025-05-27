<?php
/**
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller\user;

use app\admin\controller\Base;
use app\common\model\UserRule as UserRuleModel;
use think\facade\Cache;
use think\facade\View;
use app\admin\validate\UserRule as UserRuleValidate;
use app\common\model\UserGroup as UserGroupModel;

class UserRule extends Base
{
    protected string $indexAppName = "index";

    public function index()
    {
        $rules = $this->getRules($this->indexAppName);
        View::assign([
            'rules'=>json_encode($rules),
        ]);
        return View::fetch();
    }

    public function add(UserRuleModel $model, UserRuleValidate $validate)
    {
        if($this->request->isPost())
        {
            $check = $this->request->checkToken('__token__');
            if(false === $check) {
                //throw new ValidateException('invalid token');
                return $this->error('令牌无效');
            }

            $param = $this->request->param();

            $check = $validate->check($param);
            if(!$check)
                return $this->error($validate->getError());

            $res = $model->create($param);
            if($res)
            {
                //判断是否是菜单，如果是菜单，删除菜单缓存文件
                if($param['is_menu'] == 1)
                {
                    $this->deleteMenusCache(app()->http->getName());
                }

                //删除规则缓存文件
                $this->deleteRulesCache(app()->http->getName());

                return $this->success('添加成功', 200, [], URL_BACK);
            }

            else
            {
                return $this->error('添加失败');
            }

        }
        else
        {
            $rules = $this->getRules($this->indexAppName);
            View::assign([
                'rules'=>json_encode($rules),
            ]);
            return View::fetch();
        }
    }

    public function edit(UserRuleModel $model, UserRuleValidate $validate)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $rule = $model->find($id);
        if($this->request->isPost())
        {
            $check = $this->request->checkToken('__token__');
            if(false === $check) {
                //throw new ValidateException('invalid token');
                return $this->error('令牌无效');
            }

            $check = $validate->check($param);
            if(!$check)
                return $this->error($validate->getError());

            $res = $rule->save($param);
            if($res)
            {
                //判断是否是菜单，如果是菜单，删除菜单缓存文件
                if($param['is_menu'] == 1)
                {
                    $this->deleteMenusCache($this->indexAppName);
                }

                //删除规则缓存文件
                $this->deleteRulesCache($this->indexAppName);

                return $this->success('修改成功',200, [], URL_BACK);
            }
            else
            {
                return $this->error('修改失败', 500);
            }

        }
        else
        {
            $rules = $this->getRules($this->indexAppName);
            View::assign([
                'rule'=>$rule,
                'rules'=>json_encode($rules),
            ]);
            return View::fetch();
        }
    }

    public function del(UserRuleModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $res = $model->destroy($id);
        if($res)
        {
            //判断是否是菜单，如果是菜单，删除菜单缓存文件
            if($param['is_menu'] == 1)
            {
                $this->deleteMenusCache($this->indexAppName);
            }

            //删除规则缓存文件
            $this->deleteRulesCache($this->indexAppName);

            return $this->success('删除成功', 200, [], URL_RELOAD);
        }

        else
        {
            return $this->error('删除失败');
        }
    }

    public function multiAdd(UserRuleModel $model)
    {
        if($this->request->isPost())
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

                    return $this->success('子规则批量添加成功', 200, [], URL_RELOAD);
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
    }

    public function rules(UserRuleModel $model)
    {
        $rules = $this->getRules($this->indexAppName);
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

}