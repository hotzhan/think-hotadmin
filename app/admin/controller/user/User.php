<?php
/**
 * 会员管理
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */

namespace app\admin\controller\user;

use app\admin\controller\Base;
use app\admin\traits\UserAuthTrait;
use app\common\model\User as UserModel;
use app\common\model\UserGroup as UserGroupModel;
use app\common\validate\User as UserValidate;
use hot\token\src\tool\Random;
use think\facade\View;
use think\response\Json;

class User extends Base
{
    use UserAuthTrait;

    public function index()
    {
        $users = UserModel::withoutField(['password', 'salt'])
            ->with(['userGroupAccess.userGroup'])
            ->paginate($this->admin['pagesize']);

        $total = $users->total();
        $page = $users->render();
        View::assign([
            'users'=>$users,
            'total'=>$total,
            'page'=>$page
        ]);

        return View::fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            $validate = new UserValidate();
            $check = $validate->scene('add')->check($param);
            if(!$check)
                return $this->error($validate->getError());

            $param['salt'] = Random::alnum();
            $param['password'] = md5(md5($param['password']) . $param['salt']);


            //设置默认头像
            if(!isset($param['avatar']) || $param['avatar'] == '')
                $param['avatar'] = config('view.tpl_replace_string.__STATIC_IMG__') . '/avatar.png';

            $res = UserModel::create($param);
            if($res)
            {
                //设置角色分组
                $userAuth = $this->getUserAuth();
                $userAuth->createRole($res->id, $param['group_id']);

                return $this->success('添加成功', 200, [], 'index');
            }
            else
            {
                return $this->success('添加失败');
            }
        }
        else
        {
            $groups = UserGroupModel::where('status', 1)->select();
            View::assign([
                'groups' => $groups,
            ]);
            return View::fetch();
        }
    }

    public function edit()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $user = UserModel::find($id);
        $userAuth = $this->getUserAuth();
        if($this->request->isPost())
        {
            //密码未更新
            if($param['password'] == '')
            {
                unset($param['password']);
            }
            else
            {
                //密码规则校验
                $validate = new UserValidate();
                $chk = $validate->scene('password2')->check($param);
                if(!$chk)
                    return $this->error($validate->getError());

                $param['salt'] = Random::alnum();
                $param['password'] = md5(md5($param['password']) . $param['salt']);
            }

            //设置默认头像
            if(!isset($param['avatar']) || $param['avatar'] == '')
                $param['avatar'] = config('view.tpl_replace_string.__STATIC_IMG__') . '/avatar.png';

            $res = $user->save($param);
            if($res)
            {
                //设置角色分组
                $group = $userAuth->getGroup($id);
                $group_id = !empty($group) ? $group['group_id'] : 0;
                if($group_id != $param['group_id'])
                {
                    if($group_id == 0)
                        //如果该用户不存在角色分组，新增
                        $groupRes = $userAuth->createRole($id, $param['group_id']);
                    else
                        $groupRes = $userAuth->setRole($id, $param['group_id']);

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
            $groups = UserGroupModel::where('status', 1)->select();
            $group = $userAuth->getGroup($id);
            $group_id = !empty($group) ? $group['group_id'] : 0;
            View::assign([
                'user' => $user,
                'groups' => $groups,
                'group_id' => $group_id
            ]);
            return View::fetch();
        }
    }

    public function del(UserModel $model):Json
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

    public function disable(UserModel $model):Json
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

    public function enable(UserModel $model):Json
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