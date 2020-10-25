<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * Dispatcher
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 *
 * @property  \nb\dispatcher\Driver driver
 */
class Dispatcher extends Component {

    public static function config() {
        if(isset(Config::$o->dispatcher)) {
            return Config::$o->dispatcher;
        }
        return null;
    }

    /**
     * 请求到来，开始调度了
     */
    public static function run($data=null) {
        Debug::start();
        self::driver()->run($data);
    }

    /**
     * 指定一个类方法，以控制器的流程执行
     * @param $class 完整的类名(包含命名空间)
     * @param $function 要执行的方法名
     */
    public static function go($class,$function) {
        self::driver()->go($class,$function);
    }

}