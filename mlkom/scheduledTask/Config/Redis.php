<?php

namespace Config;

/**
 * Redis配置文件
 *
 * @package Config
 */
class Redis {

    public static $cron = array(
        'product' => array(
            'host'    => '127.0.0.1',
            'port'    => 6379
        ),
        'develop' => array(
            'host'    => '192.168.1.3',
            'port'    =>  6379
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