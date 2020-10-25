<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\exception;
use nb\Config;
use nb\Router;

/**
 * Base
 *
 * @package nb\src\exception
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/7
 */
class Base extends Driver {

    protected function show($e,$deadly = false) {
        if (Config::$o->debug && $deadly) {
            echo "\n:( Have Error\n";
            echo "CODE: {$e->getCode()} \n";
            echo "FILE: {$e->getFile()} \n";
            echo "LINE: {$e->getLine()}\n";
            echo "DESC: {$e->getMessage()}\n\n";
        }
    }

    /**
     * 当访问不存在的控制器或方法时，将回调此方法
     *
     * @param Router $router
     * @throws \Exception
     */
    public function notfound() {
        $router = Router::driver();
        quit('Cli Not Found:' .  $router->module. '/' .$router->controller . '/' . $router->function."\n");
    }
}