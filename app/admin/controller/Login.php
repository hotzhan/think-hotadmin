<?php

namespace app\admin\controller;

use app\admin\logic\Login as LoginLogic;
use app\common\traits\ConfigTrait;
use think\facade\View;

class Login extends Base
{
    use ConfigTrait;

    public function login(LoginLogic $login)
    {
        //已经登录过了
        if($login->isLogin($this->admin))
        {
            $this->redirect('index/index');
        }

        $captchaType = $this->getConfig('admin.login.captcha');
        if($this->request->isPost())
        {
            $param = $this->request->param();

            //验证码校验
            switch ($captchaType)
            {
                case 'image'://图片验证码
                    if(!captcha_check($param['captcha']))
                        return $this->error('验证码错误');
                    break;
                case 'slide'://滑块验证码
                    break;
                default://未开启验证码
                    break;
            }


            $res = $login->login($param['username'], $param['password']);
            if($res)
            {
                //判断跳转，需要记录一下退出登录前的url
                if(isset($param['redirect']) && $param['redirect'] != '')
                    $url = $param['redirect'];
                else
                    $url = 'index/index';

                return $this->success('登录中...',200,[],$url, false);
            }
            else
                return $this->error($login->getError());
        }
        else
        {
            View::assign([
                'captchaType'=>$captchaType
            ]);
            return View::fetch();
        }

    }

    public function logout(LoginLogic $login)
    {
        $login->logout();
        $this->redirect('login/login');
    }
}