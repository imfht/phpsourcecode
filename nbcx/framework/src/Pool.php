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
 * Pool
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/9/10
 */
abstract class Pool {

    /**
     * 对象池
     *
     * @access private
     * @var array
     */
    protected static $pool = [];

    /**
     * 固化
     * @var array
     */
    protected static $solidify = [];

    public static function debug($alias=null,$ex=false) {
        $de = self::$pool;
        if($alias) {
            $de = $de[$alias];
        }
        $ex?ex($de):e($de);
    }

    /**
     * 获取容器里的值
     * @param $alias
     * @return mixed|null
     */
    public static function get($alias) {
        if (isset(self::$pool[$alias])) {
            return self::$pool[$alias];
        }
        return null;
    }


    /**
     * 强制更新容器里值
     * @param $alias
     * @param $value
     */
    public static function set($alias,$value){
        return self::$pool[$alias] = $value;
    }

    /**
     * 通过一个完整的类命名来生成一个对象，然后放入内存池
     * PS：如果给生成对象的构造函数传参数，必须以数组的形式
     * @param $alias 别名/类完整名
     * @param null $namespace 类完整名/当$alias为类完整名时，也可以为构造参数
     * @param array $args 生成对象的构造参数
     * @return mixed
     * @throws \ReflectionException
     */
    public static function object($alias,$namespace=null,$args=[]) {
        if (isset(self::$pool[$alias])) {
            return self::$pool[$alias];
        }

        //object('dao','\app\Name',[]);3
        //object('dao','\app\Name');2

        //object('\app\Name',[]); 2
        //object('\app\Name');  1

        if($namespace == null) {
            $namespace = $alias;
        }
        elseif(is_array($namespace)) {
            $args = $namespace;
            $namespace = $alias;
        }

        $constructor = static::make($namespace,$args);
        /*
        $reflect = new \ReflectionClass($namespace);
        $constructor = $reflect->getConstructor();
        if (!$constructor || $constructor->getNumberOfParameters() == 0) {
            $constructor = $reflect->newInstance();
        }
        else {
            $constructor = $reflect->newInstanceArgs($args);
        }
        */
        //固化
        if(in_array($alias,Config::$o->pool)) {
            self::$solidify[$alias] = $constructor;
        }
        return self::$pool[$alias] = $constructor;
    }

    /**
     * 实例对象制作器
     * @param $namespace
     * @param array $args
     * @return object|\ReflectionMethod
     * @throws \ReflectionException
     */
    public static function make($namespace,$args=[]) {
        $reflect = new \ReflectionClass($namespace);
        $constructor = $reflect->getConstructor();
        if (!$constructor || $constructor->getNumberOfParameters() == 0) {
            $constructor = $reflect->newInstance();
        }
        else {
            $constructor = $reflect->newInstanceArgs($args);
        }
        return $constructor;
    }


    /**
     * 将一个变量放入内存池里
     * @param $alias
     * @param null $value
     * @return mixed|null
     */
    public static function value($alias,$value=''){
        if (isset(self::$pool[$alias])) {
            return self::$pool[$alias];
        }
        //value('name','hello',function ($hello){});
        //value('name',function (){});
        //value('name',new Hook());
        //value('name',2);
        //value('name',[]);
        //获取参数数量
        $num = func_num_args();

        if($num<3) {
            $value = ($value instanceof \Closure)?$value():$value;
        }
        elseif($num == 3) {
            $func = func_get_arg(2);
            $value = $func($value);
        }
        else {
            $args = func_get_args();
            $alias = $args[0];
            $value = $args[--$num];
            unset($args[0]);
            unset($args[$num]);
            $value = call_user_func_array($value,$args);
        }

        //固化
        if(in_array($alias,Config::$o->pool)) {
            self::$solidify[$alias] = $value;
        }

        return self::$pool[$alias] = $value;
    }

    /**
     * 类redis的hash存储结构
     * 本质是对一个二维数组进行操作
     *
     * @param $alias
     * @param $key
     * @param string $value
     * @return mixed|string
     */
    public static function hash($alias,$key,$value=''){
        if(isset(self::$pool[$alias]) && isset(self::$pool[$alias][$key])) {
            return self::$pool[$alias][$key];
        }
        if($value instanceof \Closure){
            $value = $value();
        }
        return self::$pool[$alias][$key] = $value;
    }

    /**
     * 强制更新hash里的数据
     * @param $alias
     * @param $key
     * @param $value
     */
    public static function hset($alias,$key,$value) {
        self::$pool[$alias][$key] = $value;
    }

    /**
     * 仅获取值，不做其它处理
     * @param $alias
     * @param $key
     * @return bool
     */
    public static function hget($alias,$key) {
        if(isset(self::$pool[$alias]) && isset(self::$pool[$alias][$key])) {
            return self::$pool[$alias][$key];
        }
        return false;
    }

    /**
     * 检测变量是否已经存在对象池
     * @param $alias
     * @return bool
     */
    public static function has($alias) {
        if( isset(self::$pool[$alias]) ) {
            return true;
        }
        return false;
    }

    /**
     * 从对象池里移除指定的对象
     * @param $class
     */
    public static function rm($alias) {
        if (isset(self::$pool[$alias])) {
            unset(self::$pool[$alias]);
        }
    }

    /**
     * 摧毁对象池
     */
    public static function destroy() {
        self::$pool = self::$solidify;
    }

    /**
     * 将对象池里的一个对象变为固化对象
     *
     * 固化对象池里的指定对象
     * 被固化的对象可以不被destroy函数销毁
     *
     * @param $alias
     */
    public static function solidify($alias) {
        if(isset(self::$pool[$alias])) {
            self::$solidify[$alias] = self::$pool[$alias];
        }
        else {
            Config::$o->pool[] = $alias;
        }
    }

    /**
     * 检测是否为固化对象
     *
     * @param $alias
     * @return bool
     */
    public static function isSolidify($alias) {
        if(isset(self::$solidify[$alias])) {
            return true;
        }
        if(in_array($alias,Config::$o->pool)) {
            return true;
        }
        return false;
    }

    /**
     * 从固化列表里移除一个固化对象
     * @param null $alias
     */
    public static function rmSolidify($alias=null) {
        if($alias === null) {
            self::$solidify = [];
        }

        if(isset(self::$solidify[$alias])) {
            unset(self::$solidify[$alias]);
        }
    }


}