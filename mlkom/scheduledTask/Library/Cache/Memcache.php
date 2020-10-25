<?php

namespace Library\Cache;

/**
 * Class Memcache
 * @package Library\Cache
 */
class Memcache {

    protected  static $_instance   = array();

    
    /**
     * 实例化的Memcache对象
     *
     * @var Memcache
     * @return \Memcache
     */
    public static function instance($name, $env='product') {
        $config = \Config\Memcache::getConfig($name, $env);

        if(!isset($config)) {
            throw new \Exception("Memcache Config not set");
        }

        if(!isset(self::$_instance[$name])) {
            if(extension_loaded('Memcache')) {
                self::$_instance[$name] = new \Memcache();
            } else {
                throw new \Exception("extension memcached is not installed");
            }

            foreach($config as $val) {
                self::$_instance[$name] ->addServer($val['host'], $val['port']);
            }
        }

        return self::$_instance[$name];
    }

}