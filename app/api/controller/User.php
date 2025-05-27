<?php
/**
 * User: hotadmin
 * Date: 2024/3/27
 * Site: https://www.hotadmin.cn
 */


namespace app\api\controller;

use app\api\middleware\LoginAuth;
use app\api\logic\Token as TokenLogic;
use app\common\model\User as UserModel;
class User extends Base
{
    /**
     * 本控制器需要登录验证
     * 访问本控制器会先进行中间件验证
     * @var string[]
     */
    protected $middleware = [LoginAuth::class];//登录验证中间件

    public function info()
    {
        $param = $this->request->param();
        $token = $param['token'];
        $tokenLogic = new TokenLogic();
        $data = $tokenLogic->decodeToken($token);
        $uid = $data->data->uid;
        $user = UserModel::field(['username', 'nickname', 'email','mobile'])->find($uid);
        if($user)
            return api_success('会员中心', 200, $user);
        else
            return api_error('用户不存在');

    }


}