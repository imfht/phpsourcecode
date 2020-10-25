<?php
//调试模式，上线后建议关闭，true为开启，false为关闭
define('Debug', true);
//MyClass目录
define('MyClass', substr(__DIR__, 0, -7));
//引入核心文件
require_once MyClass . '/vendor/autoload.php';