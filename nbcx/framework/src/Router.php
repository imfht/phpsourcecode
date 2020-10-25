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
 * URL解析类
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Router extends Component {

    public static function config() {
        // TODO: Implement config() method.
        if(Config::$o->sapi=='cli') {
            return Config::$o->argv;
        }
        return Config::$o->router;
    }

    public static function url($name, array $value = NULL, $prefix = NULL) {
        return self::driver()->url($name, $value, $prefix);
    }

    /**
     * 获取路由信息
     *
     * @param string $routeName 路由名称
     * @static
     * @access public
     * @return mixed
     */
    //public static function get($routeName) {
        //$driver = self::instance()->driver;
    //    return self::driver()->get($routeName);
    //}


}

