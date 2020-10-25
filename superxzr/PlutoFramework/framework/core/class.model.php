<?php
/**
  * PlutoFramework
  * Creat a set of generic database interaction methads.
  *
  * @author Alien <a457418121@gmail.com>
  */

abstract class Model {
  public static $db;

    /**
     * Creates a PDO connection to your datebase.
     *
     * @return boolean
     */

    public function _construct(){
        $dsn = 'mysql:dbname' . DB_NAME . ';host=' . DB_HOST;
        try {
            self::$db = new PDO($dsn,DB_USER,DB_PASS);
        } catch (PDOException $e){
            $echoError = new Error($e);
            exit;
        }

        return TRUE;
    }

    public function __destruct() {
        self::close ();
    }

    /**
     * 关闭数据库连接
     */

    public function close() {
        self::$DB = null;
    }

    public function query(){
      
    }
}
