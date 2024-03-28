<?php

namespace app\admin\controller;

use app\admin\logic\Login as AuthLogic;
use think\facade\View;

class Index extends Base
{
    public function index()
    {
        return View::fetch();
    }
}