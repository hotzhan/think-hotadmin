<?php
/**
 * api注册中间件，注册后在api应用全局生效
 * User: hotzhan
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */
 
return [
    //跨域请求支持 使用tp内置的
    //'allow_cross_domain' => think\middleware\AllowCrossDomain::class,
    //跨域请求支持 使用自定义的
    'allow_cross_domain' => app\api\middleware\AllowCrossDomain::class,
    //登录验证 权限验证，放在控制器里验证
    //'auth' => app\api\middleware\LoginAuth::class,
];