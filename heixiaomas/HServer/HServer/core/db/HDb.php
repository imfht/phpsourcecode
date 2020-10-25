<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/2/21
 * Time: 13:53
 */



namespace HServer\core\db;

use HServer\config\Config;
use Workerman\Mysql\Connection;

class HDb
{
    /**
     * 静态成品变量 保存全局实例
     */
    private static $_instance;

    /**
     * 单例模式
     */
    public static function getInstance()
    {

        $db=Config::getDB();
        if($db['flag']) {
            if (!isset(self::$_instance)) {
                self::$_instance = new Connection($db['host'], $db["port"], $db["user"], $db["password"], $db["db"]);
            }
            return self::$_instance;
        }
    }
}