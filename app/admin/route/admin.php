<?php
/**
 * User: admin
 * Date: 2024/1/18
 */
 
use think\facade\Route;

/*
 * 注意：越短的规则放后面，最短的先生效，不再读取后面路由
 * 比如 如果定义了：
 * Route::rule('general','xxx')
 * Route::rule('general/hello','xxx')
 * 那么后面的general/hello不能生效，被前面的general影响
 * Route::rule('general$','xxx') 可以加上$进行url完全匹配，这样就不影响了
 */

/*
 * :subcontroller   变量 多级控制器目录
 * :class           变量 类
 * :method          变量 方法
 * :params          变量 参数
 * $                url完全匹配
 */
Route::rule('general$', 'general.index/index');
Route::rule('general/:class$', 'general.:class/index');
Route::rule('general/:class/$', 'general.:class/index');
Route::rule('general/:class/:method$', 'general.:class/:method');
Route::rule('general/:class/:method/:params$', 'general.:class/:method/:params');

Route::rule('setting$', 'setting.index/index');
Route::rule('setting/:class$', 'setting.:class/index');
Route::rule('setting/:class/$', 'setting.:class/index');
Route::rule('setting/:class/:method$', 'setting.:class/:method');
Route::rule('setting/:class/:method/:params$', 'setting.:class/:method/:params');

Route::rule('auth$', 'auth.index/index');
Route::rule('auth/:class$', 'auth.:class/index');
Route::rule('auth/:class/$', 'auth.:class/index');
Route::rule('auth/:class/:method$', 'auth.:class/:method');
Route::rule('auth/:class/:method/:params$', 'auth.:class/:method/:params');

Route::rule('user$', 'user.index/index');
Route::rule('user/:class$', 'user.:class/index');
Route::rule('user/:class/$', 'user.:class/index');
Route::rule('user/:class/:method$', 'user.:class/:method');
Route::rule('user/:class/:method/:params$', 'user.:class/:method/:params');