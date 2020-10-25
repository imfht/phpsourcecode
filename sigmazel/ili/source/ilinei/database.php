<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

//数据库
class database{
    private static $instance;

    //获取当前数据库驱动器
    public static function instance($config = array()){
        if (!isset(self::$instance)){
            if($config['database']['driver'] == 'oracle') self::$instance = new database_oracle($config);
            elseif($config['database']['driver'] == 'pdo') self::$instance = new database_pdo($config);
            else self::$instance = new database_mysql($config);
        }

        return self::$instance;
    }
}
?>