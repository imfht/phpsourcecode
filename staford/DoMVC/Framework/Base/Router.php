<?php

/**
 * 路由，处理URL相关问题
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Router
{
    const URL_COMMON = 1;
    const URL_PATHINFO = 2;
    const URL_STATIC = 3;
    private static $str_mode = 1;

    private function __construct($url_type)
    {
        self::$str_mode = $url_type;
        switch ($url_type) {
            case 1:
                $this->parseCommon();
                break;
            case 2:
                $this->parsePathinfo();
                break;
            case 3:
                $this->parseStatic();
                break;
            default:
                $this->parseCommon();
        }
    }

    public static function init($url_type)
    {
        return new self($url_type);
    }

    private function parseCommon()
    {
        $_GET['c'] = (isset($_GET['c']) && !empty($_GET['c'])) ? $_GET['c'] : 'index'; //判断控制器，不存在则定位到默认控制器
        $_GET['a'] = (isset($_GET['a']) && !empty($_GET['a'])) ? $_GET['a'] : 'index'; //判断操作，不存在则定位到默认操作
    }

    private function parsePathinfo()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            $router = trim($_SERVER['PATH_INFO'], '/');
            if (empty($router)) {
                $c = 'index';
                $a = 'index';
            } else {
                if (!stripos($router, '/')) {
                    $c = $router;
                    $a = 'index';
                } else {
                    $router = explode('/', $router);
                    $c = array_shift($router);
                    $a = array_shift($router);
                    if (!empty($router)) {
                        $router = array_chunk($router, 2);
                        foreach ($router as $route) {
                            if (!isset($route[1])) {
                                $route[1] = '';
                            }
                            $_GET[$route[0]] = $route[1];
                        }
                    }
                }
            }
            $_GET['c'] = $c;
            $_GET['a'] = $a;
        } else {
            $_GET['c'] = 'index';
            $_GET['a'] = 'index';
        }
    }

    private function parseStatic()
    {

    }

    /**
     * URL创建器
     * @param string $controller 控制器
     * @param string $action 操作
     * @param array  $params 参数
     */
    public static function get($controller, $action, $params = array())
    {
        switch (self::$str_mode) {
            case 1:
                return self::__url_common($controller, $action, $params);
                break;
            case 2:
                return self::__url_pathinfo($controller, $action, $params);
                break;
            case 3:
                return self::__url_common($controller, $action, $params);
                break;
            default:
                return self::__url_common($controller, $action, $params);
        }
    }

    private static function __url_common($controller, $action, $params = array())
    {
        $param = '';
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $param .= '&' . $key . '=' . $val;
            }
        }
        return '/index.php?c=' . $controller . '&a=' . $action . $param;
    }
    private static function __url_pathinfo($controller, $action, $params = array())
    {
        $param = '';
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $param .= '/' . $key . '/' . $val;
            }
        }
        return '/' . $controller . '/' . $action . $param;
    }
}

?>