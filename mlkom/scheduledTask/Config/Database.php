<?php

namespace Config;

/**
 * Mysql数据库配置文件
 *
 * @package Config
 */
class Database  {

    public static $cron = array(
        'product' => array(
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => 'mg^@@!@$',
            'database' => 'system'
        ),
        'develop' => array(
            'host'     => '192.168.1.202',
            'user'     => 'root',
            'password' => 'rxg^@@!@$1987',
            'database' => 'cron'
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