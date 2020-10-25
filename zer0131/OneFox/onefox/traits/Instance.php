<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 快速实例化类
 */

namespace onefox\traits;

trait Instance {

    private static $_obj;

    /**
     * 获取实例, 支持传入参数
     * @return static
     */
    public static function getInstance() {
        if (self::$_obj) {
            return self::$_obj;
        }
        $args = func_get_args();//参数
        $className = get_called_class();//运行时调用的类名
        $ref = new \ReflectionClass($className);//反射实例化
        self::$_obj = $ref->newInstanceArgs($args);
        return self::$_obj;
    }
}
