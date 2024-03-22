<?php
/**
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */


namespace app\admin\controller\auth;

use app\admin\controller\Base;
use think\facade\View;

class AdminLog extends Base
{
    public function index()
    {
        return View::fetch();
    }
}