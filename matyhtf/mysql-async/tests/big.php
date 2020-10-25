<?php
require __DIR__.'/../Swoole/Async/MySQL.php';

$config = array(
    'host'     => '172.19.104.157',
    'user'     => 'root',
    'password' => 'root',
    'database' => 'db_live',
    'charset'  => 'utf8',
);

$pool = new Swoole\Async\MySQL($config, 100);

for($i = 0; $i < 10000; $i++)
{
    $pool->query("SELECT * FROM `access_log` WHERE id = 496", function($mysqli, mysqli_result $result){
        echo count($result->fetch_all());
        //var_dump($result->fetch_all());
    });
}
