<?php
/**
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller\auth;

use app\admin\controller\Base;
use app\admin\model\Admin as AdminModel;
use app\admin\model\AuthGroup as AuthGroupModel;
use app\admin\validate\AuthGroup as AuthGroupValidate;
use app\admin\model\AuthRule as AuthRuleModel;
use app\common\traits\AuthGroupTrait;
use think\facade\Cache;
use think\facade\View;
use think\response\Json;

class AuthGroup extends Base
{
    use AuthGroupTrait;

    public function index()
    {
        //只显示本角色分组和子分组
        $groupIds = $this->getGroupIds($this->admin['id']);
        $map = [
            ['id', 'in', $groupIds],
        ];
        $groups = AuthGroupModel::where($map)
            ->paginate($this->admin['pagesize']);
        $total = $groups->total();
        $page = $groups->render();
        View::assign([
            'groups' => $groups,
            'total' => $total,
            'page' => $page
        ]);
        return View::fetch();
    }

    public function add(AuthGroupValidate $validate, AuthGroupModel $model)
    {
        $pid = $this->getGroupPid($this->admin['id']);
        if($this->request->isPost())
        {
            $param = $this->request->param();
            //校验是否有该父分组权限
            $groupIds = $this->getGroupIds($this->admin['id']);
            if($param['pid'] != $pid && !in_array($param['pid'], $groupIds))
                return $this->error('无分组权限，请重新选择父分组');

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
            View::assign('pid', $pid);
            return View::fetch();
        }
    }

    public function edit(AuthGroupValidate $validate, AuthGroupModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $group = $model->find($id);
        if($this->request->isPost())
        {
            if($id == $param['pid'])
                return $this->error('不可选自己作为父分组');

            //校验是否有该父分组权限
            $groupIds = $this->getGroupIds($this->admin['id']);
            if($param['pid'] != $this->getGroupPid($this->admin['id']) && !in_array($param['pid'], $groupIds))
                return $this->error('无分组权限，请重新选择父分组');

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

    public function del(AuthGroupModel $model):Json
    {
        $param = $this->request->param();
        if(!isset($param['id']))
            return $this->error('请选择要操作的数据');
        $id = $param['id'];

        //判断分组是否包含数据
        $admin = AdminModel::hasWhere('authGroupAccess', [['group_id', 'in', $id]])->find();
        if($admin)
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

    public function disable(AuthGroupModel $model):Json
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

    public function enable(AuthGroupModel $model):Json
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

    public function access(AuthGroupModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];

        //校验是否该分组权限
        $groupIds = $this->getGroupIds($this->admin['id']);
        if(!in_array($id, $groupIds))
            return $this->_error('无访问权限');

        if($this->request->isPost())
        {
            $groupPid = $this->getGroupPid($this->admin['id']);
            if(isset($param['allIds']) && $groupPid == 0)
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
                $res = $group->save($param);
            else
            {
                //如果不存在就直接新建
                unset($param['id']);
                $res = $model->create($param);
            }
            if($res)
            {
                //删除对应角色组的菜单缓存
                $menusCacheKey = 'admin_menus_gid_' . $group['id'];
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

        $rules = AuthRuleModel::select();
        $rules = $this->getChild($rules, 0, 0, $type);

        $group = $this->auth->getGroupByGroupId($id);
        $ruleIds = '';
        if(isset($group['rules']))
        {
            if($group['rules'] == '*')
            {
                //*全部分组
                $ruleIds = $this->auth->getAllAuthIds();
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
        $groups = $this->getGroups($this->admin['id']);

        $data = [
            'code' => 200,
            'msg' => '角色分组列表',
            'data' => $groups,
        ];
        return json($data);
    }

    /**
     * 获取本角色分组的父ID
     * @param $uid
     * @return int
     */
    public function getGroupPid(int $uid): int
    {
        $info = AdminModel::where('id', $uid)
            ->with(['authGroupAccess.authGroup'])->find();

        return $info->authGroup->pid;
    }

    /**
     * 获取本角色分组以及子分组
     * @param $uid
     * @return array
     */
    public function getGroups(int $uid): array
    {
        $groupData = AuthGroupModel::select();
        $pid = $this->getGroupPid($uid);
        $groups = $this->getGroupChild($groupData, $pid);
        return $groups;
    }

    public function getGroupIds($uid)
    {
        $groupData = AuthGroupModel::select();

        $pid = $this->getGroupPid($uid);
        $groupIds = [];
        $this->getGroupChildIds($groupData, $groupIds, $pid);
        return $groupIds;
    }
}