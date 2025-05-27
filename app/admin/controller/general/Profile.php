<?php
/**
 * 个人资料
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */

namespace app\admin\controller\general;

use app\admin\controller\Base;
use app\admin\model\Admin;
use app\admin\logic\Login;
use app\admin\validate\Admin as AdminValidate;
use hotzhan\verify\Random;
use think\facade\View;

class Profile extends Base
{
    public function index()
    {
        $profile = Admin::where('username', 'admin')->find();
        View::assign([
            'profile'=>$profile
        ]);
        return View::fetch();
    }

    public function edit(Login $auth)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $profile = Admin::find($id);

        if(isset($param['password']))
        {
            //密码新密码规则校验
            $validate = new AdminValidate();
            $check = $validate->scene('password')->check($param);
            if(!$check)
                return $this->error($validate->getError());

            //原密码校验
            $password = md5(md5($param['password']) . $profile['salt']);
            if($password != $profile['password'])
            {
                return $this->error('原密码错误，请输入正确的原密码');
            }

            $param['salt'] = Random::alnum();
            $param['password'] = md5(md5($param['new_password']) . $param['salt']);

            $profile->token = Random::uuid();
        }
        //更新到数据库
        $res = $profile->save($param);
        if($res)
        {
            $isPjax = true;
            if(isset($param['avatar']))
            {
                $isPjax = false;
            }
            return $this->success('保存完成', 200, [], URL_RELOAD, $isPjax);
        }

        else
            return $this->error('保存失败');
    }

    public function setPassword($param)
    {

    }
}