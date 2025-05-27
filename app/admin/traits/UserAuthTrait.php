<?php

namespace app\admin\traits;

use hot\auth\Auth;
use app\common\model\User as UserModel;
use app\common\model\UserGroup as UserGroupModel;
use app\common\model\UserGroupAccess as UserGroupAccessModel;
use app\common\model\UserRule as UserRuleModel;
use hot\token\tool\Tools;

trait UserAuthTrait
{
    public function getUserAuth()
    {
        $userAuth = Auth::instance();
        //设置user的auth表
        $userAuth->setConfig([
            'auth_group' => parse_name((new UserGroupModel())->getName()) , // 用户组数据表名
            'auth_group_access' => parse_name((new UserGroupAccessModel())->getName()), // 用户-用户组关系表
            'auth_rule' => parse_name((new UserRuleModel())->getName()), // 权限规则表
            'auth_user' => parse_name((new UserModel())->getName()), // 用户信息表
        ]);

        return $userAuth;
    }

}