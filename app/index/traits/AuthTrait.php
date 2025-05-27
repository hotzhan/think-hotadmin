<?php

namespace app\index\traits;

use app\common\model\User as UserModel;
use hot\token\tool\Tools;
use think\facade\Session;
use think\facade\View;

trait AuthTrait
{
    public function checkLogin()
    {
        $user = Session::get('user');

        if(!$user)
            $this->user = null;
        else
        {
            //每次都查询数据库，影响性能，可以做到缓存里
            $userData = UserModel::find($user['id']);
            if($user['update_time'] != $userData['update_time'])
            {
                //单点登录/登录过期时间 可以放这里判断
                //修改密码后需要全部登出
                if(!isset($user['password']) || $user['password'] !== $userData->password)
                {
                    Session::clear();
                }
                else
                {
                    $this->setAdminSession($userData);
                    $this->user = $userData->toArray();
                }
            }
            else
            {
                $this->user = $user;
            }
        }
        if(!isset($this->user) || $this->user == null)
        {
            if(request()->action() !== 'login')
                $this->redirect('index/login');
        }
        else
        {
            View::assign('user', $user);
        }
    }

    public function setUserSession(UserModel $user): void
    {
        //if(isset($admin->password))
        //    unset($admin->password);
        Session::set('user', $user->toArray());
    }
    public function clearUserSession()
    {
        Session::clear();
    }

    public function checkAuth($uid)
    {
        if($uid == 0)
            $this->_error('无访问权限', 0);

        $controller = parse_name($this->request->controller());
        $action = strtolower($this->request->action());
        $path = str_replace('._', '/', $controller) . '/' . $action;

        if(!in_array($action, $this->noNeedRight))
        {

            $check = $this->auth->check($path, $uid);
            if(!$check)
            {
                $this->_error('无访问权限', 0);
            }
        }
    }
}