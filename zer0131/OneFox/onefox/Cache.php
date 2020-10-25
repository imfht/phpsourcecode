<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 缓存抽象类
 */

namespace onefox;

abstract class Cache {
    protected static $instance;
    protected $options = [];

    public static function getInstance($type = '') {
        if (!self::$instance) {
            $type = $type ? $type : Config::get('cache.type', 'file');
            $class = "\\onefox\\Caches\\" . 'C' . ucwords(strtolower($type));
            self::$instance = new $class();
        }
        return self::$instance;
    }

    abstract public function get($name);//获取缓存

    abstract public function set($name, $value, $expire = null);//设置缓存

    abstract public function rm($name, $ttl = 0);//删除缓存

    abstract public function clear();//清除缓存
}
