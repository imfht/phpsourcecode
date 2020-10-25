<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 类单例实现
 */

namespace onefox\traits;

trait Singleton {
    private static $_instance = null;

    /**
     * 获取单例
     * @return static
     */
    public static function getSingleton() {
        if (!self::$_instance) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    private function __construct() {}

    private function __clone() {}

    private function __wakeup() {}
}
