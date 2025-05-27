<?php
/**
 * User: admin
 * Date: 2024/1/23
 */


namespace app\facade;

use think\Facade;

/**
 * @see \app\common\library\Url
 * @package think\facade
 * @mixin \app\common\library\Url
 * @method static string url(string $url, array $vars = [], $suffix = true, $domain = false) 解析Url
 */
class Url extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\library\Url';
    }
}