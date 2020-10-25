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
 * 组件抽象定义类
 *
 * @package nb
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/7/25
 */
abstract class Component {

    public $driver;

    public function __construct() {
        $args = func_get_args();
        $args[] = static::config();
        $this->driver =  call_user_func_array(
            'static::create',
            $args
        );
    }

    /**
     * 创建并返回一个驱动对象
     * 此函数创建的对象，非单列对象
     */
    public static function create(...$args) {
        $alias = get_called_class();

        $config = end($args)?:[];
        $class = self::parse($alias,$config);

        return Pool::make($class,$args);
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

        return Pool::set($key,$class);
    }

    public static function replace() {

    }

    /**
     *
     * @param $class 子类全路径
     * @param null $args
     * @throws \ReflectionException
     */
    protected static function parse($class,$config) {
        //检查是否有指定包名，如没有，使用子类全称为包名
        $class = isset($config['namespace'])?$config['namespace']:strtolower($class);
        if(isset($config['driver']) && $config['driver']) {

            if(strpos($config['driver'], '\\') !== false) {
                $class = $config['driver'];
            }
            else {
                //如果无'/'，表明驱动为内置类
                $class .= '\\' . ucfirst($config['driver']);
            }
            return $class;
        }
        $driver = $class . '\\' . ucfirst(Config::$o->sapi);

        if(class_exists($driver)) {
            $class = $driver;
        }
        else {
            $class .= '\\Base';
        }
        return $class;
    }

    /**
     * 根据组件类名获取框架配置里的设置
     * @return mixed|null
     */
    public static function config() {
        $key = explode('\\',get_called_class());
        $key = strtolower(end($key));
        if(isset(Config::$o->$key)) {
            return Config::$o->$key;
        }
        return null;
    }

    /**
     * 清除内存池里的驱动对象
     */
    public static function remove() {
        Pool::remove(get_called_class());
    }

    /**
     * 对类库里的方法静态调用
     * @param $name
     * @param $arguments
     * @return self
     */
    public static function __callStatic($method, $arguments) {
        // TODO: Implement __callStatic() method.
        return call_user_func_array([static::driver(),$method],$arguments);
    }

    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
        return call_user_func_array([$this->driver,$name],$arguments);
    }

    public function __set($name, $value) {
        // TODO: Implement __set() method.
        return $this->driver->$name = $value;
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        return $this->driver->$name;
    }

    //驱动包名
    protected static function __namespace() {
        return get_called_class();
    }

}
