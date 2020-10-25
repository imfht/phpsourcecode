<?php
namespace ebcms;

class Route
{

    public static function route()
    {
        if (!$routes = \think\Cache::get('eb_routes')) {
            $routes = self::get_route();
            \think\Cache::set('eb_routes',$routes);
        }
        if (is_array($routes)) {
            foreach ($routes as $key => $route) {
                \think\Route::rule($route[0], $route[1], $route[2], $route[3], $route[4]);
            }
        }
    }

    private static function get_route()
    {
        $routes = [];
        // 注入自定义路由
        $customs = \ebcms\Config::get('home.route.rules');
        $customs = explode(PHP_EOL, \ebcms\Func::streol($customs));
        foreach ($customs as $custom) {
            if (strpos($custom, '|')) {
                $custom = explode('|', $custom);
                $routes[] = [$custom[0], $custom[1], 'GET|POST', [], []];
            }
        }

        // 应用模式
        if (1 == \ebcms\Config::get('home.route.model')) {
            $apps = get_app();
            foreach ($apps as $key => $app) {
                $routefile = APP_PATH.$app['name'].'/install/route.php';
                if (is_file($routefile)) {
                    $temp = include $routefile;
                    if (is_array($temp)) {
                        $routes = array_merge($routes,$temp);
                    }
                }
            }
        }
        return $routes;
    }
}