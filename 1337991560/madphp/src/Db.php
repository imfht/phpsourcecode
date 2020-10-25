<?php

/**
 * Db
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Db
{
    private function __construct()
    {

    }

    public static function pdo($dbname, $config = array(), $type = "mysql")
    {
        $pdoFactory = new Db\PdoFactory;
        return $pdoFactory->create($dbname, $config, $type);
    }

    public static function mongo($dbname, $config = array())
    {
        $mongoFactory = new Db\MongoFactory();
        return $mongoFactory->create($dbname, $config);
    }
}