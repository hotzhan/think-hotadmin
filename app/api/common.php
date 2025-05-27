<?php
/**
 * User: hotadmin
 * Date: 2024/3/27
 * Site: https://www.hotadmin.cn
 */

use think\response\Json;

if(!function_exists('api_result'))
{
    function api_result(string $msg='', int $code = 500, $data = [], string $url = URL_CURRENT,  int $wait = 3, array $header = [], array $option = []) : Json
    {
        $rdata = [
            'msg'   => $msg,
            'code'  => $code,
            'data'  => empty($data) ? (object)$data : $data,
            'url'   => $url,
            'wait' => $wait,
        ];

        return \json($rdata, 200, $header, $option);
    }
}

if(!function_exists('api_success'))
{
    function api_success(string $msg='', int $code = 200, $data = [], string $url = URL_CURRENT, int $wait = 3, array $header = [], array $option = []) : Json
    {
        return api_result($msg, $code,$data,  _url($url), $wait, $header, $option);
    }
}

if(!function_exists('api_error'))
{
    function api_error(string $msg='', int $code = 500, $data = [], string $url = URL_CURRENT, int $wait = 3, array $header = [], array $option = []) : Json
    {
        return api_result($msg, $code, $data, _url($url), $wait, $header, $option);
    }
}