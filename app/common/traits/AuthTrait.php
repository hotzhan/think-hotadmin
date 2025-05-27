<?php

namespace app\common\traits;

use hot\token\tool\Tools;

trait AuthTrait
{
    public function checkAuth(int $uid)
    {
        if($uid == 0)
            $this->_error('无访问权限', 0);

        $controller = parse_name($this->request->controller());
        $action = strtolower($this->request->action());
        $path = str_replace('._', '/', $controller) . '/' . $action;

        if(!in_array($action, $this->noNeedRight))
        {
            $check = $this->auth->check($path, $uid);
            if(!$check)
            {
                $this->_error('无访问权限', 0);
            }
        }
    }
}