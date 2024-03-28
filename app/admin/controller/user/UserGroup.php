<?php
/**
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller\user;

use app\admin\controller\Base;
use app\admin\traits\UserAuthTrait;
use app\common\model\UserRule as UserRuleModel;
use app\common\traits\AuthGroupTrait;
use app\common\model\UserGroup as UserGroupModel;
use app\common\model\User as UserModel;
use app\admin\validate\AuthGroup as AuthGroupValidate;
use hot\auth\Auth;
use think\facade\Cache;
use think\facade\View;
use app\index\logic\UserRule as UserRuleLogic;
use think\response\Json;

class UserGroup extends Base
{
    use AuthGroupTrait;
    use UserAuthTrait;

    public function index()
    {
        $groups = UserGroupModel::paginate($this->admin['pagesize']);
        $total = $groups->total();
        $page = $groups->render();

        View::assign([
            'groups' => $groups,
            'total' => $total,
            'page' => $page,
        ]);

        return View::fetch();
    }

    public function add(AuthGroupValidate $validate, UserGroupModel $model)
    {
        //这里和admin的AuthGroup的不同，不用校验父ID，直接添加即可
        if($this->request->isPost())
        {
            $param = $this->request->param();
            if($validate->check($param))
            {
                $res = $model->create($param);
                if($res)
                    return $this->success('添加成功', 200, [], 'index');
                else
                    return $this->error('添加失败');
            }
            else
            {
                return $this->error($validate->getError());
            }
        }
        else
        {
            return View::fetch();
        }
    }

    public function edit(AuthGroupValidate $validate, UserGroupModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $group = $model->find($id);
        if($this->request->isPost())
        {
            if($id == $param['pid'])
                return $this->error('不可选自己作为父分组');

            if($validate->scene('edit')->check($param))
            {
                $res = $group->save($param);
                if($res)
                    return $this->success('修改成功', 200, [], 'index');
                else
                    return $this->error('修改失败');
            }
            else
            {
                return $this->error($validate->getError());
            }
        }
        else
        {
            View::assign('group', $group);
            return View::fetch();
        }
    }

    public function del(UserGroupModel $model):Json
    {
        $param = $this->request->param();
        if(!isset($param['id']))
            return $this->error('请选择要操作的数据');
        $id = $param['id'];

        $user = UserModel::hasWhere('userGroupAccess', [['group_id', 'in', $id]])->find();
        halt($user);
        if($user)
            return $this->error('该分组下还有用户，不可删除');

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

        $res = $model->destroy(static function ($query) use ($id){
            /** @var \think\db\Query $query */
            $query->whereIn('id', $id);
        });
        if($res)
        {
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

    public function disable(UserGroupModel $model):Json
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

    public function enable(UserGroupModel $model):Json
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

    public function access(UserGroupModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];

        if($this->request->isPost())
        {
            if(isset($param['allIds']))
            {
                //全部选中，必须是顶级角色分组才有效
                $ruleIdsStr = '*';
            }
            else
            {
                $ruleIds = $param['ruleIds'];
                sort($ruleIds);
                $ruleIdsStr = implode(',', $ruleIds);
            }

            $param['rules'] = $ruleIdsStr;
            $group = $model->find($id);
            if($group)
            {
                $res = $group->save($param);
            }
            else
            {
                unset($param['id']);
                $res = $model->create($param);
            }
            if($res)
            {
                $menusCacheKey = 'index_menus_gid_' . $group['id'];
                Cache::delete($menusCacheKey);
                return $this->success('保存成功', 200, [], 'index');
            }
            else
                return $this->error('保存失败');
        }
        else
        {
            View::assign('id', $id);
            return View::fetch();
        }
    }

    /**
     * 规则列表
     * @return \think\response\Json
     */
    public function accessRules()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $type = $param['type'];
        $type = $type == 1 ? 1 : 0;

        $rules = UserRuleModel::select();
        $rules = $this->getChild($rules, 0, 0, $type);

        $userAuth = $this->getUserAuth();
        $group = $userAuth->getGroupByGroupId($id);
        $ruleIds = '';
        if(isset($group['rules']))
        {
            if($group['rules'] == '*')
            {
                //*全部分组
                $ruleIds = $userAuth->getAllAuthIds();
            }
            else
            {
                $ruleIds = $group['rules'];
            }

        }

        $data = [
            'code' => 200,
            'msg' => '菜单信息',
            'data' => $rules,
            'ruleIds' => $ruleIds
        ];
        return json($data);
    }

    public function groups()
    {
        $groups = $this->getGroups();

        $data = [
            'code' => 200,
            'msg' => '角色分组列表',
            'data' => $groups,
        ];
        return json($data);
    }

    /**
     * 获取本角色分组以及子分组
     * @param $uid
     * @return array
     */
    public function getGroups(): array
    {
        $groupData = UserGroupModel::select();
        $groups = $this->getGroupChild($groupData);
        return $groups;
    }
}