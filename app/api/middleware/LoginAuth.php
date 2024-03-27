<?php
/**
 * User: hotadmin
 * Date: 2024/3/27
 * Site: https://www.hotadmin.cn
 */


namespace app\api\middleware;

use app\api\logic\Login;
use app\Request;
use think\exception\HttpResponseException;

/**
 * 登录验证中间件
 */
class Auth
{
    public function handle(Request $request, \Closure $next)
    {
        $loginLogic = new Login();
        try {
            //var_dump($request->header('token'));
            $loginLogic->checkLogin($request->header('token'));
        }
        catch (\Exception $e){
            throw new HttpResponseException(api_error($e->getMessage()));
        }

        return $next($request);
    }
}