<?php
/**
 * 登录注销验证
 * User: hotadmin
 * Date: 2024/2/1
 * Site: https://www.hotadmin.cn
 */


namespace app\index\logic;

use app\common\model\User as UserModel;
use app\common\model\UserGroupAccess as UserGroupAccessModel;
use hotzhan\verify\Random;
use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Session;
use think\facade\Validate;
use think\facade\View;
use hot\safe\SafeCookie;

class Login
{
    protected static $instance;

    protected string $error = '';

    protected string $deviceIdPrefix = 'device_id_';
    protected bool $singleDevice = false;

    protected string $loginSignKey = 'user_login_sign';

    protected string $signKey = '__default sign key__';

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

    public function register($param)
    {
        if(!isset($param['nickname']) || $param['nickname'] == '')
            $param['nickname'] = $param['username'];
        $param['salt'] = \hot\token\src\tool\Random::alnum();
        $param['password'] = md5(md5($param['password']) . $param['salt']);
        $param['avatar'] = '/static/img/avatar.png';

        $model = new UserModel();
        $res = $model->create($param);
        if($res)
        {
            //还要添加对应的授权
            $data['uid'] = $res->id;
            $data['group_id'] = 1;//默认组ID 1
            UserGroupAccessModel::create($data);
        }

        return $res;
    }

    public function login(string $username, string $password, bool $remember)
    {
        //识别用户名是格式邮箱还是手机号如果都不是就默认username，在支持邮箱/手机号/用户名登录的情况可用
        $field = Validate::is($username, 'email') ? 'email' : (Validate::is($username, 'mobile') ? 'mobile' : 'username');
        $user = UserModel::where($field, $username)->with('userGroupAccess')->find();
        if($user)
        {
            $passwd = md5(md5($password) . $user->salt);
            if($passwd !== $user->password)
            {
                $this->setError('账号或者密码错误');
                return false;
            }
            else
            {
                $user->ip = request()->ip();
                $user->login_time = time();
                //$user->token = Random::uuid();
                $user->save();

                $this->setLoginInfo($user, $remember);

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
        $this->clearLoginInfo();
    }

    /**
     * 控制器的方法是否需要鉴权
     * @param $arr
     * @return bool
     */
    public function noNeedLogin($arr): bool
    {
        $array = is_array($arr) ? $arr : explode(',', $arr);
        if($array)
        {
            $array = array_map('parse_name', $array);
            $controller = parse_name(request()->controller());
            $action = parse_name(request()->action());
            if(in_array($controller . '/' . $action, $array) || in_array('*', $array))
            {
                return true;
            }
        }

        return false;
    }

    public function setLoginInfo($user, bool $remember)
    {
        //单设备登录
        if($this->singleDevice)
        {
            $this->setDeviceId($user['id']);
        }

        //记住我
        if($remember)
        {
            $this->setLoginSign($user['id']);
            SafeCookie::set('user_id', $user['id']);
        }
        Session::set('user', $user->toArray());
    }
    public function clearLoginInfo()
    {
        Session::clear();
        //Session::delete('user');
        Cookie::delete($this->loginSignKey);//删除记住登录状态loginSign
    }

    public function isLogin($user = null): bool
    {
        //$admin = Session::get('admin');
        if(isset($user['id']))
            return true;
        else
            return false;
    }

    public function checkLogin(&$userResult)
    {
        $user = Session::get('user');
        //dump($user);

        $userData = null;
        if(!$user)
        {
            //记住登录状态校验
            $userId = SafeCookie::get('user_id');
            if($userId && $this->checkLoginSign($userId))
            {
                $user = $userData = UserModel::where('id', $user['id'])->with('userGroupAccess')->find();
            }
            else
            {
                $this->setError('请登录');
                return false;
            }

        }
        //用户信息有更新
        if($user['update_time'] != Cache::get('uid_'.$user['id'].'_update_time'))
        {
            $newUserData = $userData ?? UserModel::where('id', $user['id'])->with('userGroupAccess')->find();
            //修改密码后需要全部登出
            if(!isset($user['password']) || $user['password'] !== $newUserData->password)
            {
                $this->clearLoginInfo();
                $this->setError('密码已修改，请重新登录');
                return false;
            }

            if($newUserData->status != 1)
            {
                $this->clearLoginInfo();
                $this->setError('账号已冻结');
                return false;
            }

            Session::set('user', $newUserData->toArray());//更新Session
            $user = $newUserData;
        }


        //单设备登录校验
        if($this->singleDevice && !$this->checkDeviceId($user['id']))
        {
            $this->setError('账号已在其它地方登录');
            return false;
        }

        $userResult = $user;
        View::assign('user', $user);

        return true;
    }

    public function setLoginSign(int $uid):void
    {
        //这里只判断客户端cookie的值，最好服务的也有保存对应的数据进行校验
        $loginSign = $this->getLoginSign($uid);
        Cookie::forever($this->loginSignKey, $loginSign);//user_login_sign
    }

    public function getLoginSign(int $uid):string
    {
        return md5(md5($this->signKey . $this->loginSignKey . $uid ) . $this->signKey);
    }

    public function checkLoginSign(int $uid):bool
    {
        $clientLoginSign = Cookie::get($this->loginSignKey);
        $loginSign = $this->getLoginSign($uid);
        if($clientLoginSign != $loginSign)
            return false;
        else
            return true;

    }

    public function setDeviceId(int $uid)
    {
        $uuid = Random::uuid();
        $deviceId = sha1($this->deviceIdPrefix . $uid . $uuid . microtime());
        $key = $this->deviceIdPrefix . $uid;
        Cookie::set($key, $deviceId);
        Cache::set($key, $deviceId);
    }

    public function getDeviceId(int $uid)
    {
        return Cache::get($this->deviceIdPrefix . $uid);
    }

    public function checkDeviceId(int $uid):bool
    {
        $key = $this->deviceIdPrefix . $uid;
        $clientDeviceId = Cookie::get($key);
        $serverDeviceId = Cache::get($key);
        if($clientDeviceId != $serverDeviceId)
            return false;
        else
            return true;
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