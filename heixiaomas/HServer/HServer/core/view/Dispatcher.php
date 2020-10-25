<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/8/4
 * Time: 2:09
 */

namespace HServer\core\view;

use HServer\core\http\Request;
use HServer\core\http\Response;
use HServer\core\ioc\Container;


class Dispatcher
{


    public static function display(Response $resp, Request $req)
    {
        $paths = explode("/", $req->getFullUri());
        $path = null;
        $size = count($paths);
        if ($size > 2) {
            $boo = strpos($paths[$size - 1], "?");
            if ($boo > 0) {
                $paths[$size - 1] = substr($paths[$size - 1], 0, $boo);
            }
        }
        $classname = $paths[$size - 2];
        for ($i = 0; $i < $size - 1; $i++) {
            if (strlen($paths[$i]) > 0) {
                $path .= "/" . $paths[$i];
            }
        }

        $path = __DIR__ . "/../../../app/action" . $path . ".php";
        if (count($paths) > 2 && is_file($path)) {
            /**
             * 判断容器，
             */
            if (Container::exist($classname)) {
                try {
                    $Obj = Container::getBean($classname);
                    $setResponse = "setResponse";
                    $setRequest = "setRequest";
                    $method = $paths[$size - 1];
                    $Obj->$setRequest($req);
                    $Obj->$setResponse($resp);
                    $Obj->$method();
                } catch (\Throwable $exception) {
                    $resp->send("404->" . $exception->getMessage());
                }
                return;
            }

            /**
             * 首次加载
             */
            $class = new \ReflectionClass($classname);
            $controller = $class->newInstanceArgs();
            Container::addBean($classname, $controller);
            if ($class->hasMethod($paths[$size - 1])) {
                try {
                    $setResponse = $class->getMethod("setResponse");
                    $setRequest = $class->getMethod("setRequest");
                    /**
                     * 反射传入request和response
                     */
                    $setRequest->setAccessible(true);
                    $setRequest->invoke($controller, $req);
                    $setResponse->setAccessible(true);
                    $setResponse->invoke($controller, $resp);

                    $method = $class->getMethod($paths[$size - 1]);
                    $method->invoke($controller);
                } catch (\Throwable $exception) {
                    $resp->send("404->" . $exception->getMessage());
                }
            } else {
                $resp->send("404");
            }
        } else {
            $resp->send("无法访问控制器--->首页路径");
        }
    }

}