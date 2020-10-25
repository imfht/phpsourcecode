<?php

class Conn
{

    static $pdo;

    private function __construct ()
    {}

    private function __clone ()
    {}

    public static function getDb ()
    {
        $config = require 'database.local.php';
        
        foreach ($config as $k => $v) {
            $dbConfig[$k] = $v;
        }
        
        if ($dbConfig['type'] == '') {
            $dbConfig['type'] = 'mysql';
        }
        
        $connect = $dbConfig['type'] . ":host=" . $dbConfig['host'] . ";port=" .
                 $dbConfig['port'] . ";dbname=" . $dbConfig['dbname'];
        
        try {
            if ($dbConfig['type'] == 'mysql') {
                $charset = "SET NAMES '" . $dbConfig['charset'] . "'";
                $pdo = new Pdo($connect, $dbConfig['username'], 
                        $dbConfig['password'], 
                        array(
                                PDO::MYSQL_ATTR_INIT_COMMAND => $charset,
                                PDO::ATTR_PERSISTENT => $dbConfig['presistent']
                        ));
            } else {
                $pdo = new Pdo($connect, $dbConfig['username'], 
                        $dbConfig['password']);
            }
        } catch (PDOException $e) {
            echo "数据库没有连接,如果还未安装程序,请先运行根目录下的install.php";
            throw new Exception('Connection failed: ' . $e->getMessage());
        }
        return $pdo;
    }

    public static function getInstance ()
    {
        if (self::$pdo == null) {
            self::$pdo = self::getDb();
        }
        
        return self::$pdo;
    }
}