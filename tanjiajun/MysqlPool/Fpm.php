<?php
/**
 * Fpm方式数据库长连接
 * @author tanjiajun
 * Date: 2018/9/10
 * Time: 12:35
 */

$dbConfig = array(
    'host' => 'mysql:host=10.0.2.2:3306;dbname=test',
    'port' => 3306,
    'user' => 'root',
    'password' => 'root',
    'database' => 'test',
    'charset' => 'utf8',
    'timeout' => 2,
);
$db = new PDO($this->dbConfig['host'], $this->dbConfig['user'], $this->dbConfig['password'], PDO::ATTR_PERSISTENT);
$ret = $db->query('select * from guestbook limit 1');