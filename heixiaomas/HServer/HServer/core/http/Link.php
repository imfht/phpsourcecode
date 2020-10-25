<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/4/10
 * Time: 12:51
 */

namespace HServer\core\http;

use HServer\core\ioc\Container;

class Link
{

    public static function invoke($req, $resp)
    {

        /**
         * 容器查询看看有没有，有就拿出来用
         */
        $benName = "Filter";
        if (Container::exist($benName)) {
            try {
                $map = Container::getBean($benName);
                foreach ($map as $m) {
                    $setResponse = "setResponse";
                    $setRequest = "setRequest";
                    $method = "auth";
                    $Obj = $m['filter'];
                    $Obj->$setRequest($req);
                    $Obj->$setResponse($resp);
                    $Obj->$method();
                }
            } catch (\Throwable $exception) {
                echo "404->" . $exception->getMessage();
            }
            return;
        }

        $map = array();
        /**
         * 扫描Filter文件路径
         */
        $path = __DIR__ . "/../../../app/filter/";
        $filterFile = scandir($path);
        foreach ($filterFile as $filename) {
            if ($filename != '.' && $filename != '..' && $filename . strpos($filename, 'php') !== false) {
                $classname = substr($filename, 0, -4);

                $class = new \ReflectionClass($classname);
                $filter = $class->newInstanceArgs();
                if ($class->hasMethod("auth")) {

                    $setRequest = $class->getMethod("setRequest");
                    $setRequest->setAccessible(true);
                    $setRequest->invoke($filter, $req);

                    $setResponse = $class->getMethod("setResponse");
                    $setResponse->setAccessible(true);
                    $setResponse->invoke($filter, $resp);

                    $level = $class->getProperty('level');
                    $level->setAccessible(true);
                    $index = $level->getValue($filter);

                    $a = array("level" => $index, "filter" => $filter, "class" => $class);
                    $map[] = $a;
                    array_multisort(array_column($map, 'level'), SORT_DESC, $map);

                } else {
                    echo "无拦截器";
                }
            }
        }

        Container::addBean($benName, $map);
        foreach ($map as $m) {
            $auth = $m['class']->getMethod("auth");
            $auth->setAccessible(true);
            $auth->invoke($m['filter']);
        }
    }


}