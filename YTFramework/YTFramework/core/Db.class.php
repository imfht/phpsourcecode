<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Db.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  数据库连接
 * =============================================================================                   
 */

namespace core;

class Db
{

    protected static $objInstance;

    private function __construct()
    {
        
    }

    public static function getInstance()
    {
        if (!self::$objInstance) {
            $database = Config::get('database');
            self::$objInstance = new \PDO($database['DB.dsn'], $database['DB.username'], $database['DB.password'], array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING));
        }

        return self::$objInstance;
    }

    function __clone()
    {
        
    }

}
