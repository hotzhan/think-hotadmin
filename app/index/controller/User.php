<?php
/**
 * User: hotadmin
 * Date: 2024/2/23
 * Site: https://www.hotadmin.cn
 */


namespace app\index\controller;

use hot\token\src\tool\Random;
use hotzhan\verifycode\mail\MailVerifyCode;
use hotzhan\verifycode\sms\SmsVerifyCode;
use think\facade\Cache;
use think\facade\View;
use app\index\validate\Login as LoginValidate;
use app\common\model\User as UserModel;

class User extends Base
{
    public function index()
    {
        return View::fetch();
    }

    public function edit()
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            $id = $param['id'];
            $type = $param['type'];
            $user = UserModel::find($id);
            switch ($type)
            {
                case 'modify_password':
                    $param = $this->password($param, $user);
                    if( isset($param['code']) && $param['code'] == 500 &&  isset($param['msg']) && $param['msg'] != '' )
                    {
                        return $this->error($param['msg']);
                    }
                    break;
                case 'active_email':
                    $param = $this->email($param, $user);
                    if( isset($param['code']) && $param['code'] == 500 &&  isset($param['msg']) && $param['msg'] != '' )
                    {
                        return $this->error($param['msg']);
                    }
                    break;
                case 'edit_email':
                    $param = $this->email($param, $user);
                    if( isset($param['code']) && $param['code'] == 500 &&  isset($param['msg']) && $param['msg'] != '' )
                    {
                        return $this->error($param['msg']);
                    }
                    break;
                case 'active_mobile':
                    $param = $this->mobile($param, $user);
                    if( isset($param['code']) && $param['code'] == 500 &&  isset($param['msg']) && $param['msg'] != '' )
                    {
                        return $this->error($param['msg']);
                    }
                    break;
                case 'edit_mobile':
                    $param = $this->mobile($param, $user);
                    if( isset($param['code']) && $param['code'] == 500 &&  isset($param['msg']) && $param['msg'] != '' )
                    {
                        return $this->error($param['msg']);
                    }
                    break;
                default:
                    break;
            }


            //更新到数据库
            $res = $user->save($param);
            if($res)
                return $this->success('保存完成', 200, [], URL_RELOAD, false);
            else
                return $this->error('保存失败');

        }


    }


    public function password($param, $user)
    {
        //密码新密码规则校验
        $validate = new LoginValidate();
        $check = $validate->scene('modify_password')->check($param);
        if(!$check)
            return ['code'=>500, 'msg'=>$validate->getError()];

        //原密码校验
        $password = md5(md5($param['password']) . $user['salt']);
        if($password != $user['password'])
        {
            return ['code'=>500, 'msg'=>'原密码错误，请输入正确的原密码'];
        }

        $param['salt'] = Random::alnum();
        $param['password'] = md5(md5($param['new_password']) . $param['salt']);

        return $param;
    }

    public function email($param, $user)
    {
        //密码新密码规则校验
        $validate = new LoginValidate();
        $check = $validate->scene('regmail')->check($param);
        if(!$check)
            return ['code'=>500, 'msg'=>$validate->getError()];

        $mail = new MailVerifyCode();
        $res = $mail->checkMailVerifyCode($param['mail_token'], $param['mail_code'], $param['email']);
        if(!$res)
            return $mail->getResultData();

        $param['email_status'] = 1;

        return $param;
    }
    public function mobile($param, $user)
    {
        //密码新密码规则校验
        $validate = new LoginValidate();
        $check = $validate->scene('regsms')->check($param);
        if(!$check)
            return ['code'=>500, 'msg'=>$validate->getError()];

        $sms = new SmsVerifyCode();
        $res = $sms->checkSmsVerifyCode($param['sms_token'], $param['sms_code'], $param['mobile']);
        if(!$res)
            return $sms->getResultData();
        $param['mobile_status'] = 1;

        return $param;
    }
}