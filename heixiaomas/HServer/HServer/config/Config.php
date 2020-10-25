<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/2/21
 * Time: 13:56
 */


namespace HServer\config;

class Config
{
    /**
     * 配置redis,默认不连接
     * @return array
     */
    public static function getRedis()
    {
        $redis = array();
        $redis["host"] = "127.0.0.1";
        $redis["port"] = 6379;
        //无密码留空
        $redis["password"] = "123456a.";
        //是否开启redis
        $redis['flag'] = false;
        return $redis;
    }

    /**
     * 配置Mysql，默认不连接
     * @return array
     */
    public static function getDB()
    {
        $db = array();
        $db["host"] = "127.0.0.1";
        $db["user"] = "root";
        $db["password"] = "haosql";
        $db["db"] = "av";
        $db["port"] = 3306;
        //是否开启db
        $db['flag'] = false;
        return $db;
    }

}