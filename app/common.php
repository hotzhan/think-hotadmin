<?php
// 应用公共文件 其实和助手函数文件类似 参考topthink/framework/src/helper.php

use app\facade\Url;

/** 不做任何操作 */
const URL_CURRENT = 'url://current';
/** 刷新页面 */
const URL_RELOAD = 'url://reload';
/** 返回上一个页面 */
const URL_BACK = 'url://back';
/** 关闭当前layer弹窗 */
const URL_CLOSE_LAYER = 'url://close_layer';
/** 关闭当前弹窗并刷新父级 */
const URL_CLOSE_REFRESH = 'url://close_refresh';

if(!function_exists('_url'))
{
    /**
     * 助手函数url的升级版，适用于多级控制器的Url解析
     * @param string $url
     * @param array $vars
     * @param $suffix
     * @param $domain
     * @return string
     */
    function _url(string $url, array $vars = [], $suffix = true, $domain = false): string
    {
        if($url == URL_CURRENT || $url == URL_RELOAD || $url == URL_BACK || $url == URL_CLOSE_LAYER || $url == URL_CLOSE_REFRESH ||
        strstr($url, 'http://') || strstr($url, 'https://')
        )
            return $url;
        return Url::url($url, $vars, $suffix, $domain);
    }
}

if(!function_exists('add_prefix'))
{
    /**
     * 字符串加前缀，如果已经有该前缀就不加
     * @param string $str
     * @param string $prefix
     * @return string
     */
    function add_prefix(string $str, string $prefix = 'hot_'):string
    {
        if($str != '' && substr($str, 0, strlen($prefix)) != $prefix)
        {
            return $prefix . $str;
        }
        else
        {
            return $str;
        }
    }
}


if(!function_exists('fileSizeToString'))
{
    function fileSizeToString($size)
    {
        // 获取文件大小（单位为字节）
        //$sizeInBytes = filesize($file);
        $sizeInBytes = $size;

        if ($sizeInBytes >= pow(1024, 3)) {
            $sizeString = round($sizeInBytes / pow(1024, 3), 2) . " GB";
        } else if ($sizeInBytes >= pow(1024, 2)) {
            $sizeString = round($sizeInBytes / pow(1024, 2), 2) . " MB";
        } else if ($sizeInBytes >= 1024) {
            $sizeString = round($sizeInBytes / 1024, 2) . " KB";
        } else {
            $sizeString = $sizeInBytes . " bytes";
        }

        return $sizeString;
    }
}

if(!function_exists('formatTimestamp'))
{
    function formatTimestamp(int $timestamp)
    {
        $subtime = $timestamp;
        $day = $subtime > 86400 ? floor($subtime / 86400) : 0;
        $subtime -= $day * 86400;
        $hour = $subtime > 3600 ? floor($subtime / 3600) : 0;
        $subtime -= $hour * 3600;
        $min = $subtime > 60 ? floor($subtime / 60) : 0;
        $subtime -= $min * 60;
        $sec = $subtime;

        $dateText = $day > 0 ? $day . '天' : '';
        $dateText .= $hour > 0 ? $hour . '小时' : '';
        $dateText .= $min > 0 ? $min . '分钟' : '';
        $dateText .= $sec > 0 ? $sec . '秒' : '';

        return $dateText;
    }
}