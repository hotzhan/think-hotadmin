<?php
/**
 * User: admin
 * Date: 2024/1/18
 */


namespace app\api\controller;

use think\captcha\facade\Captcha;

class Index extends Base
{
    public function index()
    {
        return api_success('首页数据',200,[['id'=>1001,'title'=>'标题1'],['id'=>1002,'title'=>'标题2']]);
    }

    public function captcha()
    {
        return Captcha::create();
    }
}