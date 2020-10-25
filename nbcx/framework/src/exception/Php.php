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
use nb\I18n;
use nb\Router;

/**
 * 处理php-fpm模式下的异常
 *
 * @package nb\exception
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/7
 */
class Php extends Driver {

    protected function show($e,$deadly = false) {
        if (Config::$o->debug && $deadly) {
            if(ob_get_level() > 0) {
                $obget = ob_get_contents();
                ob_clean();
            }
            include __DIR__ . DS . 'html' . DS . 'exception.tpl.php';
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
        if (isset($this->call['notfound']) && !call_user_func($this->call['notfound'], $router)) {
            return;
        }
        if(ob_get_level() > 0) {
            ob_clean();
        }
        //if (!headers_sent()) {
        //    header('HTTP/1.1 404 Not Found');
        //    header('Status:404 Not Found');
        //}
        //if (f('ajax')) {
        //    Config::$o->debug and quit('Ajax Not Found:' . $router->getModel(). '/' . $router->getController() . '/' . $router->getFunction());
        //    quit('404 page not found url!');
        //}
        if (Config::$o->debug) {
            $hint = I18n::t('请求无法应答！');
            $message = I18n::t('请检查下面路由信息是否正确！ %s%s%s%s',[
                '<br/>module : '.$router->module,
                '<br/>folder : '.$router->folder,
                '<br/>controller : '.$router->controller,
                '<br/>function : '.$router->function
            ]);
        }
        include __DIR__ . DS . 'html' . DS . 'hint.tpl.php';
    }

}