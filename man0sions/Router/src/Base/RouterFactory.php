<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/9/26
 * Time: 上午10:52
 */

namespace LuciferP\Router\Base;


use LuciferP\Http\Request;
use LuciferP\Http\Response;
use LuciferP\Router\Router;

class RouterFactory
{
    private static $router = null;

    public static function getRouter()
    {
        $request = new Request();
        $response = new Response();
        $reslove = new \LuciferP\Router\ResloveRequest();

        if (self::$router == null) {
            self::$router = new Router($request,$response,$reslove);
        }

        return self::$router;
    }
}