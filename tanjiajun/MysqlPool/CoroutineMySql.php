<?php

/**
 * 协程Mysql客户端Demo
 * @author tanjiajun
 */
go(function () {
    $start = microtime(true);
    $db = new Swoole\Coroutine\MySQL();
    $db->connect([
        'host' => '10.0.2.2',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'test',
        'timeout' => 6#1
    ]);
    $db->query("select sleep(5)");
    echo "我是第一个sleep五秒之后\n";
    $ret = $db->query("select user from guestbook limit 1");#2
    var_dump($ret);
    $use = microtime(true) - $start;
    echo "协程mysql输出用时：" . $use . PHP_EOL;
});