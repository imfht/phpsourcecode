<?php

/*应用入口文件*/
if(file_exists('./Install') && !file_exists('./Install/install.lock')){

	header('location:Install/index.php');

}

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', false);

//项目名称
define('APP_NAME', "App");

// 定义应用目录
define('APP_PATH', './App/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';