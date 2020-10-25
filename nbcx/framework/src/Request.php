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
 * Request
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Request extends Component {

    public static function config() {
        if(isset(Config::$o->request)) {
            return Config::$o->request;
        }
        return null;
    }

    /**
     * 获取驱动对象，以单列的模式保存在对象池里
     * @return driver
     */
    public static function driver() {
        $key = get_called_class();
        if($driver = Pool::get($key)) {
            return $driver;
        }
        $args = func_get_args();
        $config = static::config();
        $config and array_unshift($args,$config);
        $class =  call_user_func_array(
            'static::create',
            $args
        );
        $request = Pool::set($key,$class);
        Pool::object('nb\event\Framework')->request($request);
        return $request;
    }

    /**
     * 获取请求数据
     *
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function form($method='request',array $args=null) {
        return self::driver()->form($method,$args);
    }

    /**
     * 获取请求数据
     *
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function formx($method='request',array $args=null) {
        return new Collection(self::form($method,$args));
    }

    /**
     * 获取表单参数对应的值
     * 如果获取多个，则以值数组的形式返回
     *
     * @param $arg
     * @param array ...$args
     * @return array|mixed|null
     */
    public static function input(...$args){
        return call_user_func_array([self::driver(),'input'],$args);
    }

}