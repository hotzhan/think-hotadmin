<?php
/**
 * User: hotadmin
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */


namespace app\api\controller;

use app\common\controller\Base as CommonBase;
use think\App;
use app\api\logic\Login as LoginLogic;

class Base extends CommonBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);

        //放到中间件里验证
        //$loginAuth = LoginLogic::instance();
        //$loginAuth->checkLogin(request()->header('authorization'));
    }
}