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
 * Session
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Session extends Component {

    //Session设置
    //public $config = [
    //    'driver'=>'',
    //    'name'=>'',
    //    'path'=>'',
    //    'id'             => '',
    //    'var_session_id' => '',// SESSION_ID的提交变量,解决flash上传跨域
    //    'prefix'         => 'nb_',// SESSION 前缀
    //    'storage'           => '',// 驱动方式 支持redis memcache memcached
    //    'auto_start'     => true,// 是否自动开启 SESSION
    //];

    /**
     * @var \nb\session\Driver
     */
    //public $driver;

    //use \nb\library\Instance;


    public static function config() {
        // TODO: Implement config() method.
        return Config::$o->session;
    }

    /**
     * session设置
     * @param string $name session名称
     * @param mixed $value session值
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function set($name, $value = '', $prefix = '') {
        self::driver()->set($name, $value, $prefix);
    }

    /**
     * session获取
     * @param string $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public static function get($name = '', $prefix = '') {
        return self::driver()->get($name, $prefix);
    }

    /**
     * session获取并删除
     * @param string $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public static function pull($name, $prefix = '') {
        return self::driver()->pull($name, $prefix);
    }

    /**
     * session设置 下一次请求有效
     * @param string $name session名称
     * @param mixed $value session值
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function flash($name, $value) {
        return self::driver()->flash($name, $value);
    }

    /**
     * 清空当前请求的session数据
     * @return void
     */
    public static function flush() {
        self::driver()->flush();
    }

    /**
     * 删除session数据
     * @param string|array $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function delete($name, $prefix = null) {
        self::driver()->delete($name, $prefix);
    }

    /**
     * 清空session数据
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function clear() {
        self::driver()->clear();
    }

    /**
     * 判断session数据
     * @param string $name session名称
     * @param string|null $prefix
     * @return bool
     */
    public static function has($name, $prefix = null) {
        return self::driver()->has($name,$prefix);
    }

    /**
     * 添加数据到一个session数组
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public static function push($key, $value) {
        self::driver()->push($key,$value);
    }

    /**
     * 启动session
     * @return void
     */
    public static function start() {
        self::driver()->start();
    }

    /**
     * 销毁session
     * @return void
     */
    public static function destroy() {
        self::driver()->destroy();
    }

}
