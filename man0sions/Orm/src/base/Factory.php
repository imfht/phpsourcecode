<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/25
 * Time: 下午7:52
 */

namespace LuciferP\Orm\base;


use LuciferP\Orm\db\Proxy;

/**
 * 简单工厂,构造全局单例对象
 * Class Factory
 * @package LuciferP\Orm\base
 */
class Factory
{
    private static $db;

    /**
     * 配合代理Proxy 实现 sql 自动读写分离
     * @param string $type
     * @return null
     */
    public static function getDb($type = 'proxy')
    {
        $db_conf = Registry::get('db_conf');
        if ($type == 'proxy') {
            $key = "\\LuciferP\\Orm\\db\\Proxy";
            $db = Registry::get($key);

            if (!$db) {
                $db = new $key();
                Registry::set($key, $db);
            }
            return $db;
        } elseif ($type == 'slave') {
            $rand = array_rand($db_conf['slave']);
            $conf = $db_conf['slave'][$rand];

        } elseif ($type == 'master') {
            $conf = $db_conf['master'];
        }
        $key = "\\LuciferP\\Orm\\db\\Pdo";

        static::$db = new $key();
        static::$db->connect($conf['host'], $conf['user'], $conf['passwd'], $conf['dbname']);
        return static::$db;
    }


}