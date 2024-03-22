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
use app\admin\model\AuthGroupAccess as AuthGroupAccessModel;
use app\admin\validate\Admin as AdminValidate;
use hotzhan\verify\Random;
use think\facade\Config;
use think\facade\View;
use think\response\Json;

class Admin extends Base
{
    public function index()
    {
        $map = [];
        if($this->admin['pid'] != 0)
        {
            //可以管理同级和子级管理员，如果pid=0顶级管理员可以管理所有
            $map = [
                ['pid', '=', $this->admin['pid']],
                ['pid', '=', $this->admin['id']]
            ];
        }

        $admins = AdminModel::whereOr($map)
            ->withoutField(['password','salt'])
            ->with(['authGroupAccess.authGroup'])//先关联Admin模型的authGroupAccess，再关联AuthGroupAccess模型的authGroup
            ->paginate($this->admin['pagesize']);

        $total  = $admins->total();
        $page = $admins->render();
        $auth = $this->auth;//分组名称也可以通过 {$id|$auth->getRole}方式，不过每次都需要一个数据库查询，效率太低
        View::assign([
            'admins'=>$admins,
            'total'=>$total,
            'page'=>$page,
            'auth'=>$auth,
        ]);
        return View::fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            /*
             * 在validate里加token验证，无需这里验证
            $check = $this->request->checkToken('__token__');
            if(false === $check) {
                throw new ValidateException('invalid token');
            }
            */

            $param = $this->request->param();
            //规则校验，username里加了token表单令牌验证
            $validate = new AdminValidate();
            $check = $validate->scene('add')->check($param);
            if(!$check)
                return $this->error($validate->getError());

            $param['salt'] = Random::alnum();
            $param['password'] = md5(md5($param['password']) . $param['salt']);

            //设置默认头像
            if(!isset($param['avatar']) || $param['avatar'] == '')
                $param['avatar'] = Config::get('view.tpl_replace_string.__STATIC_IMG__') . '/avatar.png';

            //父ID
            $param['pid'] = $this->admin['id'];

            $res = AdminModel::create($param);
            if($res)
            {
                return $this->success('添加成功', 200, [], 'index');
            }
            else
            {
                return $this->success('添加失败');
            }
        }
        else
        {
            $groups = AuthGroupModel::where('status', 1)->select();
            View::assign([
                'groups' => $groups,
            ]);
            return View::fetch();
        }
    }

    public function edit(AdminValidate $validate)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $admin = AdminModel::find($id);
        if($this->request->isPost())
        {
            /*
             * 在validate里加token验证，无需这里验证
            $check = $this->request->checkToken('__token__');
            if(false === $check) {
                throw new ValidateException('invalid token');
            }
            */

            //规则校验，username里加了token表单令牌验证
            $chk = $validate->scene('edit')->check($param);
            if(!$chk)
                return $this->error($validate->getError());

            //密码未更新
            if($param['password'] == '')
            {
                unset($param['password']);
            }
            else
            {
                //如果修改了密码，进行密码规则校验
                $chk = $validate->scene('password2')->check($param);
                if(!$chk)
                    return $this->error($validate->getError());

                $param['salt'] = Random::alnum();
                $param['password'] = md5(md5($param['password']) . $param['salt']);
            }

            $res = $admin->save($param);
            if($res)
            {
                //设置角色分组
                $group = $this->auth->getGroup($id);
                $group_id = !empty($group) ? $group['group_id'] : 0;
                if($group_id != $param['group_id'])
                {
                    //至少需要保留1个开发管理员，否则后台功能可能无法全面设置
                    if($group_id == 1)
                    {
                        $count = AuthGroupAccessModel::where('group_id', 1)->count();
                        if($count == 1)
                            return $this->error('只剩一个开发管理员了，不支持当前用户修改分组');
                    }

                    if($group_id == 0)
                        //如果该用户不存在角色分组，新增
                        $groupRes = $this->auth->createRole($id, $param['group_id']);
                    else
                        $groupRes = $this->auth->setRole($id, $param['group_id']);

                    if($groupRes < 1)
                        return $this->error('修改角色分组失败');
                }

                return $this->success('修改成功', 200, [], URL_BACK);
            }
            else
                return $this->error('修改失败');
        }
        else
        {
            $groups = AuthGroupModel::where('status', 1)->select();
            $group = $this->auth->getGroup($id);
            $group_id = !empty($group) ? $group['group_id'] : 0;
            View::assign([
                'admin_user' => $admin,
                'groups' => $groups,
                'group_id' => $group_id
            ]);
            return View::fetch();
        }
    }

    public function del(AdminModel $model):Json
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

    public function disable(AdminModel $model):Json
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

    public function enable(AdminModel $model):Json
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