<?php
/**
 * User: hotadmin
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */
 
use think\facade\Route;

//路由全局跨域
//Route::middleware('allow_cross_domain');//指定中间件配置的跨域
//Route::allowCrossDomain();//tp默认的跨域


//单独路由跨域

//默认跨域
//Route::get('captcha/[:config]','\\think\\captcha\\CaptchaController@index')->allowCrossDomain();

//指定中间件中的跨域
//Route::get('captcha/[:config]','\\think\\captcha\\CaptchaController@index')->middleware('allow_cross_domain');

//直接用控制器中间件
//Route::get('user','user/index')->middleware(['allow_cross_domain', 'auth']);
//Route::get('user/<action?>','user/<action?>')->middleware(['allow_cross_domain', 'auth']);
