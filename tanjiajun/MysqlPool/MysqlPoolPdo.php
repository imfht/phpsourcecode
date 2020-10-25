<?php
/**
 * 数据库连接池PDO方式
 * @author tanjiajun
 * Date: 2018/9/8
 * Time: 11:30
 */
require "AbstractPool.php";

class MysqlPoolPdo extends AbstractPool
{
    protected $dbConfig = array(
        'host' => 'mysql:host=10.0.2.2:3306;dbname=test',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'test',
        'charset' => 'utf8',
        'timeout' => 2,
    );
    public static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MysqlPoolPdo();
        }
        return self::$instance;
    }

    protected function createDb()
    {
        return new PDO($this->dbConfig['host'], $this->dbConfig['user'], $this->dbConfig['password']);
    }
}

$httpServer = new swoole_http_server('0.0.0.0', 9501);
$httpServer->set(
    ['worker_num' => 1]
);
$httpServer->on("WorkerStart", function () {
    MysqlPoolPdo::getInstance()->init();
});
$httpServer->on("request", function ($request, $response) {
    $db = null;
    $obj = MysqlPoolPdo::getInstance()->getConnection();
    if (!empty($obj)) {
        $db = $obj ? $obj['db'] : null;
    }
    if ($db) {
        $db->query("select sleep(2)");
        $ret = $db->query("select * from guestbook limit 1");
        MysqlPoolPdo::getInstance()->free($obj);
        $response->end(json_encode($ret));
    }
});
$httpServer->start();