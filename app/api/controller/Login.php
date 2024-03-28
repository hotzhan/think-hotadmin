<?php
/**
 * User: hotadmin
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */


namespace app\api\controller;

use app\api\exception\ApiException;
use app\api\logic\Login as LoginLogic;
use app\api\logic\Token;
use think\response\Json;

class Login extends Base
{
    public function login():Json
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            //var_dump($param);
            //if(!isset($param['captcha']) || !captcha_check($param['captcha']))
            //{
                //return $this->error('验证码错误');
            //}
            $loginLogic = new LoginLogic();
            try
            {
                $data = $loginLogic->login($param['username'], $param['password'], false);
                return api_success('登录成功', 200, $data);
            }
            catch (ApiException $e)
            {
                return api_error($e->getMessage());
            }
        }

        return api_error('请求异常');
    }

    public function refresh()
    {
        halt(request()->header());

    }
}