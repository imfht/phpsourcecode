<?php
// 检测程序安装
if (!is_file(__DIR__ . '/install/install.lock')) {
    header('Location: ./install/index.php');
    exit;
}

define('APP_PATH', __DIR__ . '/app/');

define('BIND_MODULE', 'index');

require_once __DIR__ . '/esphp/base.php';
