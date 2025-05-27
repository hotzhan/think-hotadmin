<?php
/**
 * 登录逻辑类
 * User: hotazhan
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */


namespace app\api\logic;

use app\api\exception\ApiException;
use app\common\model\User as UserModel;
use think\exception\HttpResponseException;
use think\facade\Validate;

class Login
{
    protected static $instance;

    public static function instance($options = [])
    {
        if(is_null(self::$instance))
        {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    public function checkLogin(string $token)
    {
        $tokenLogic = new Token();
        try {
            $tokenLogic->checkToken($token);
        }
        catch (ApiException $e){
            throw new ApiException('未登录');
        }
    }
    public function login(string $username, string $password, bool $remember):array
    {
        //识别用户名是格式邮箱还是手机号如果都不是就默认username，在支持邮箱/手机号/用户名登录的情况可用
        $field = Validate::is($username, 'email') ? 'email' : (Validate::is($username, 'mobile') ? 'mobile' : 'username');
        $user = UserModel::where($field, $username)->with('userGroupAccess')->find();
        if($user)
        {
            $passwd = md5(md5($password) . $user->salt);
            if($passwd !== $user->password)
            {
                throw new ApiException('账号或者密码错误');
            }
            else
            {
                $user->ip = request()->ip();
                $user->login_time = time();
                $user->save();

                $tokenLogic = new Token();
                $accessToken = $tokenLogic->createAccessToken($user->id);
                $refreshToken = $tokenLogic->createRefreshToken($user->id);

                return [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken
                ];
            }
        }
        else
        {
            throw new ApiException('账号或者密码错误');
        }
    }

}