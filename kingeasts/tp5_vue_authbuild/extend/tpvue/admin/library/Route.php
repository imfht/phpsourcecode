<?php
// +----------------------------------------------------------------------
// | TpAndVue.
// +----------------------------------------------------------------------
// | FileName: Router.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin\library;


use think\facade\Route as ThinkRoute;
use tpvue\admin\http\middleware\AuthMiddleware;
use tpvue\admin\http\middleware\MemberLoginMiddleware;

class Route
{
    public static function add($rule, $controller, $noAuth = false)
    {
        $tmp = explode('/', $controller);
        $action = array_pop($tmp);
        if (count($tmp) === 1) {
            $tmps = array_pop($tmp);
            $controller = ucwords($tmps) . 'Controller';
            $ctrl = $tmps . '/' . $action;
        } else {
            $tmps = array_pop($tmp);
            $pc = ucwords($tmps) . 'Controller';
            $controller = implode('\\', $tmp) . '\\' . $pc;
            $ctrl = implode('/', $tmp) . '/' . $tmps . '/' . $action;
        }
        $path = 'admin' . ($rule === '/' ? '' : ('/' . $rule)) . '$';
        $name = 'admin/' . $ctrl;

        if ($noAuth) {
            $middlewares = [];
        } else {
            $middlewares = [
                MemberLoginMiddleware::class,
                AuthMiddleware::class
            ];
        }
        return ThinkRoute::rule([$name, $path], 'tpvue\\admin\\controller\\' . $controller . '/' . $action)->middleware($middlewares)->option([
            '__ke__'=>true,
            '__rule__'=>$name
        ]);
    }

}