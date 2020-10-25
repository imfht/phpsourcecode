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
 * Cookie
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/7/25
 */
class Cookie extends Component {

    /**
     * @var \nb\cookie\Driver
     */
    //public $driver;

    //use \nb\library\Instance;

    public static function config() {
        // TODO: Implement config() method.
        return Config::$o->cookie;
    }

    /**
     * 设置或者获取cookie作用域（前缀）
     * @param string $prefix
     * @return string|void
     */
    public static function prefix($prefix = '') {
        $that = self::driver();
        $that->prefix($prefix);
        if (empty($prefix)) {
            return $that->config['prefix'];
        }
        $that->config['prefix'] = $prefix;
    }

    /**
     * Cookie 设置、获取、删除
     *
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     *
     * @return mixed
     */
    public static function set($name, $value = '', $option = null) {
        self::driver()->set($name, $value, $option);
    }

    /**
     * 永久保存Cookie数据
     * @param string $name cookie名称
     * @param mixed $value cookie值
     * @param mixed $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    public static function forever($name, $value = '', $option = null) {
        self::driver()->forever($name, $value, $option);
    }

    /**
     * 判断Cookie数据
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return bool
     */
    public static function has($name, $prefix = null) {
        return self::driver()->has($name, $prefix);
    }

    /**
     * Cookie获取
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public static function get($name, $prefix = null) {
        //$driver = self::instance()->driver;
        return self::driver()->get($name, $prefix);
    }

    /**
     * Cookie删除
     * @param string $name cookie名称
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public static function delete($name, $prefix = null) {
        //$driver = self::instance()->driver;
        self::driver()->delete($name, $prefix);
    }

    /**
     * Cookie清空
     * @param string|null $prefix cookie前缀
     * @return mixed
     */
    public static function clear($prefix = '') {
        //$driver = self::instance()->driver;
        self::driver()->clear($prefix);
    }

}
