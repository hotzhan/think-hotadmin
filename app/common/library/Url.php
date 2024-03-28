<?php
/**
 * User: admin
 * Date: 2024/1/23
 * 自定义版Url地址生成类
 * 对Url解析更细致，可适用多级控制器的Url解析
 */
declare (strict_types = 1);

namespace app\common\library;

use think\route\Url as UrlBuild;
use hot\token\tool\Tools;

class Url extends UrlBuild
{
    protected $appName;
    /**
     * 直接解析URL地址
     * 重写Url地址解析函数，可适用多级控制器的Url解析
     * @access protected
     * @param  string      $url URL
     * @param  string|bool $domain Domain
     * @return string
     */
    protected function parseUrl(string $url, &$domain): string
    {
        $request = $this->app->request;

        if (0 === strpos($url, '/')) {
            // 直接作为路由地址解析
            $url = substr($url, 1);
        } elseif (false !== strpos($url, '\\')) {
            // 解析到类
            $url = ltrim(str_replace('\\', '/', $url), '/');
        } elseif (0 === strpos($url, '@')) {
            // 解析到控制器
            $url = substr($url, 1);
        } elseif ('' === $url) {
            $url = $request->controller() . '/' . $request->action();
            if (!$this->app->http->isBind()) {
                $url = $this->getAppName() . '/' . $url;
            }
        } else {
            // 解析到 应用/控制器/操作
            $controller = $request->controller();
            $path = explode('/', $url);

            if(count($path)<3)
            {
                //当解析Url层级数<3时，可以按多应用原来一样解析
                $action     = array_pop($path);
                $controller = empty($path) ? $controller : array_pop($path);
                if(strstr($controller, '.'))
                {
                    //var_dump($controller);
                    //如果是多级控制器的，将.转换为/
                    $multiCont = explode('.', $controller);
                    $multiCont[1] = parse_name($multiCont[1]);//如果view.php 默认模板渲染规则auto_rule不是默认1时，这句可以不要
                    $controller = $multiCont[0] . '/' . $multiCont[1];
                    //var_dump($controller);
                }
                //$app        = empty($path) ? $this->getAppName() : array_pop($path);
                $app = $this->getAppName();
                $url        = $controller . '/' . $action;
            }
            else
            {
                //当解析Url层级数>=3时，就不能用获取方法控制应用名这种层级获取了
                //如果后续有Url解析变更，在这里改动即可
                $app = $this->getAppName();
                if($path[0] == $app)
                {
                    //如果第一个是应用名，则去掉应用名，后面会有判断是否做了子域名绑定应用的情况
                    array_shift($path);
                    $url = implode('/', $path);
                }
            }

            $bind = $this->app->config->get('app.domain_bind', []);

            if ($key = array_search($this->app->http->getName(), $bind)) {
                isset($bind[$_SERVER['SERVER_NAME']]) && $domain = $_SERVER['SERVER_NAME'];

                $domain = is_bool($domain) ? $key : $domain;
            } elseif (!$this->app->http->isBind()) {
                $url = $app . '/' . $url;
            }

        }

        return $url;
    }

    /**
     * 解析Url外部调用，类似Route::buildUrl()功能
     * @param string $url
     * @return string
     */
    public function url(string $url, array $vars = [], $suffix = true, $domain = false)
    {
        $this->url = $url;
        return $this->vars($vars)->suffix($suffix)->domain($domain)->build();//build()里调用了parseUrl()
    }
    public function appUrl(string $appName, string $url, array $vars = [], $suffix = true, $domain = false)
    {
        $this->setAppName($appName);
        $appUrl = $this->url($url, $vars, $suffix, $domain);
        $this->appName = null;
        return $appUrl;
    }

    /**
     * 获取URL的应用名
     * @access protected
     * @return string
     */
    protected function getAppName()
    {
        if(!$this->appName)
            $this->setAppName();
        return $this->appName;
    }

    protected function setAppName($appName = '')
    {
        if($appName == '')
        {
            $app = $this->app->http->getName();
        }
        else
        {
            $app = $appName;
        }
        $map = $this->app->config->get('app.app_map', []);

        if ($key = array_search($app, $map)) {
            $app = $key;
        }
        $this->appName = $app;
    }

}
