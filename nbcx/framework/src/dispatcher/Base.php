<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\dispatcher;

use nb\Config;
use nb\Pool;
use nb\Request;
use nb\Router;

/**
 * Custom
 *
 * @package nb\dispatcher
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/7
 */
class Base extends Driver {

    /**
     * 处理网络请求
     */
    public function run() {
        $router = Router::driver()->mustAnalyse();

        //如果访问的模块，加载模块配置
        $router->module and $this->module($router->module);

        //如果加载不成功，作为404处理
        //过滤掉禁止访问的方法
        $class = $router->class;
        if(!$class || in_array($router->function,Config::$o->notFunc)) {
            return Pool::object('nb\event\Framework')->notfound();
        }
        $this->go($class,$router->function);
    }

    protected function input(\ReflectionClass $controller, $app) {
        //获取此次请求的参数
        return Request::driver()->form();
    }

}