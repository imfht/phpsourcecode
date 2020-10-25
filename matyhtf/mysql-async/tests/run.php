<?php
require __DIR__.'/../Swoole/Async/MySQL.php';

$config = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    'database' => 'test',
);

$pool = new Swoole\Async\MySQL($config, 100);

for($i = 0; $i < 10000; $i++)
{
    $pool->query("show tables", function($mysqli, mysqli_result $result){
        var_dump($result->fetch_all());
    });
}