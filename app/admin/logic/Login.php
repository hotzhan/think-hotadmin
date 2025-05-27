<?php
/**
 * 登录注销验证
 * thinkphp service目录作为服务的目录使用了，Service也作为服务相关的类
 * 避免和thinkphp的服务冲突，业务逻辑层这里就用logic目录和Logic类替代了
 * User: hotadmin
 * Date: 2024/2/1
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\logic;

use app\admin\model\Admin as AdminModel;
use hot\token\src\tool\Random;
use think\facade\Event;
use think\facade\Session;
use think\facade\View;

class Login
{
    protected static $instance;

    protected string $error = '';

    /**
     * 单例模式
     * @param $options
     * @return Login|static
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    public function login(string $username, string $password)
    {
        //识别用户名是格式邮箱还是手机号如果都不是就默认username，在支持邮箱/手机号/用户名登录的情况可用
        //$field = Validate::is($param['username'], 'email') ? 'email' : (Validate::is('mobile') ? 'mobile' : 'username');
        $admin = AdminModel::where('username', $username)->with('authGroupAccess')->find();
        if($admin)
        {
            $passwd = md5(md5($password) . $admin->salt);
            if($passwd !== $admin->password)
            {
                $this->setError('账号或者密码错误');
                return false;
            }
            else
            {
                $admin->ip = request()->ip();
                $admin->login_time = time();
                $admin->save();
                Session::set('admin', $admin->toArray());
                //session('admin', $admin);
                Event::trigger('AdminLogin');
                return true;
            }
        }
        else
        {
            $this->setError('账号或者密码错误');
            return false;
        }
    }
    public function logout(): void
    {
        Session::clear();
    }

    public function isLogin($admin = null): bool
    {
        //$admin = Session::get('admin');
        if(isset($admin['id']) && $admin['id']>0)
            return true;
        else
            return false;
    }

    public function checkLogin(&$adminResult)
    {
        $admin = Session::get('admin');
        //dump($admin);
        if(!$admin)
            $adminResult = null;
        else
        {
            //每次都查询数据库，影响性能，可以做到缓存里
            $adminData = AdminModel::where('id', $admin['id'])->with('authGroupAccess')->find();
            if($admin['update_time'] != $adminData['update_time'])
            {
                //单点登录/登录过期时间 可以放这里判断
                //修改密码后需要全部登出
                if(!isset($admin['password']) || $admin['password'] !== $adminData->password)
                {
                    Session::clear();
                }
                else
                {
                    $adminResult = $adminData->toArray();
                    Session::set('admin', $adminResult);
                }
            }
            else
            {
                $adminResult = $admin;
            }
        }

        if(!isset($adminResult) || $adminResult == null)
        {
            return false;
        }
        else
        {
            View::assign('admin', $admin);
            return true;
        }
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }
}