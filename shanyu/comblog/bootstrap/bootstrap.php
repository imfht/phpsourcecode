<?php
// 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 初始化环境
require __DIR__.'/constant.php';

// 辅助函数
require __DIR__.'/helper.php';

// 环境配置
date_default_timezone_set('Asia/Shanghai');

// 加载环境
(new Dotenv\Dotenv(__DIR__.'/../','.env'))->load();

// 错误处理
Kernel\Error::register();


// Debugger
if(strtolower(getenv('APP_DEBUG')) == 'true'){
    Tracy\Debugger::enable();
}

// 执行应用
(new Kernel\App())->run();

