<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'         => env('APP_HOST', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [
        'adminxx'=>'admin'
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [
        'api.tp8.com'=>'api',
        //'admin2.tp8.com'=>'admin'
    ],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => true,

    'controller_auto_search' => true,

    'dispatch_error_template' => app()->getAppPath() . 'common/view/tpl/dispatch_jump.tpl',

    'http_exception_template'    =>  [
        // 定义404错误的模板文件地址
        404 =>  app()->getAppPath()  . 'common/view/tpl/404.html',
        // 还可以定义其它的HTTP status
        401 =>  app()->getAppPath()  . 'common/view/tpl/401.html',
        1000 =>  \think\facade\App::getAppPath() . 'common/view/tpl/dispatch_jump.tpl',
    ],

];
