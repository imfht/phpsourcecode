<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/21
 * Time: 下午3:53
 */
class EZMongoDB
{

    public $conn;

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZMongoDB();
        }
        return self::$instance;
    }

    function __construct(){
        try {
            $this->conn = new MongoClient(EZConfig()->MONGO_DSN);
        } catch (PDOException $e) {
            EZErr::errException(500, $e);
            $this->pdo = null;
        }
    }

}