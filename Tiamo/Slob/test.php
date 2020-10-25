<?php
//测试状态服务器
require_once "config.php";
$key="test1";
$tick = \App\StatsCenter::tick($key,123);
sleep(1);
$tick->report(true);
