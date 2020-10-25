<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/21
 * Time: 下午1:44
 */
class EZSqlPdo
{

    public $pdo;

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZSqlPdo();
        }
        return self::$instance;
    }

    function __construct(){
        try {
            $this->pdo = new PDO(EZConfig()->PDO_DB_DSN, EZConfig()->PDO_DB_USER, EZConfig()->PDO_DB_PWD, EZConfig()->PDO_DB_OPTIONS); //establish connection
        } catch (PDOException $e) {
            EZErr::errException(500, $e);
            $this->pdo = null;
        }
    }
}