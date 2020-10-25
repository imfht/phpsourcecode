<?php

namespace Config;

/**
 * Memcache 配置文件
 *
 * @package Config
 */
class Memcache {

    public static $cron = array(
        'product' => array(
            array(
                'host'    => '127.0.0.1',
                'port'    => 11211
            )
        ),
        'develop' => array(
            array(
                'host'    => '192.168.1.3',
                'port'    => 11211
            )
        )
    );

    public static function getConfig($name, $section='product') {
        $config = self::$$name;

        if(empty($config)) {
            throw new \Exception("配置文件不存在");
        }

        return $config[$section];
    }
}