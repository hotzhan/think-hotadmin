<?php
/**
 *  登录验证中间件
 *  可以放在路由里，也可以放在需要登录验证的控制器里
 * User: hotadmin
 * Date: 2024/3/27
 * Site: https://www.hotadmin.cn
 */


namespace app\api\middleware;

use app\api\logic\Login;
use app\Request;
use think\exception\HttpResponseException;

class LoginAuth
{
    public function handle(Request $request, \Closure $next)
    {
        //客户端提交的token在header头里，字段key为token
        if(is_null($request->header('token')))
            throw new HttpResponseException(api_error('未登录'));
        $loginLogic = new Login();
        try {
            $loginLogic->checkLogin($request->header('token'));
        }
        catch (\Exception $e){
            throw new HttpResponseException(api_error($e->getMessage()));
        }

        return $next($request);
    }
}