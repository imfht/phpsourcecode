<?php
/**
 * 缓存实例化工厂类(缓存集合set)
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;
use ReflectionClass;
use ReflectionException;

class CacheFactory {

    private static $INSTANES = array();

    /**
     * 创建缓存
     * @param $classPath
     * @return ICache
     * @throws ReflectionException
     */
    public static function create($classPath) {

        if ( !isset(self::$INSTANES[$classPath]) ) {
            $reflect = new ReflectionClass($classPath);
            $instance = $reflect->newInstance();
            $instance->initConfigs();
            self::$INSTANES[$classPath] = $instance;
        }
        return self::$INSTANES[$classPath];
    }
}
