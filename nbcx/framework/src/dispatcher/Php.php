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
 * Php
 *
 * @package nb\dispatcher
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Php extends Driver {

    /**
     * 处理网络请求
     */
    public function run() {
        //判断是否为模块绑定
        $module = Config::$o->module_bind;
        if($module && isset($module[$host = Request::driver()->host])) {
            $this->module($module[$host]);
            $router = Router::driver();
            $router->module = $module[$host];
            $router->mustAnalyse();
        }
        else {
            $router = Router::driver()->mustAnalyse();
            //如果访问的模块，加载模块配置
            if($router->module) {
                $this->module($router->module);
            }
        }

        //如果请求的Action为Debug，则打开debug页面
        switch ($router->controller) {
            case 'debug':
                $this->debug($router);
                break;
            default :
                //如果加载不成功，作为404处理
                //过滤掉禁止访问的方法
                $class = $router->class;//$this->load($router);
                if(!$class || in_array($router->function,Config::$o->notFunc)) {
                    return Pool::object('nb\event\Framework')->notfound();
                }
                //过滤掉禁止访问的方法
                //if (in_array($router->function,Config::$o->notFunc)) {
                //    return Pool::object('nb\\event\\Framework')->notfound();
                //}
                $this->go($class,$router->function);
                break;
        }
    }

    protected function input(\ReflectionClass $controller, $app) {
        //获取此次请求的参数
        $method = 'request';
        if ($controller->hasProperty('_method')) {
            $method = $app->_method;
        }
        return Request::driver()->form($method);
    }

    /**
     * 处理debug
     * @param NRouter $url
     */
    protected function debug(\nb\router\Driver &$url) {
        if (Config::$o->debug) {
            \nb\Debug::driver()->index();
            return;
        }
        Pool::object('nb\event\Framework')->notfound($url);
    }



}