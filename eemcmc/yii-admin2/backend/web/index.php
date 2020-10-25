<?php

//导入加载类
require(__DIR__ . '/../../vendor/autoload.php');

//导入环境配置类
require(__DIR__ . "/../../common/config/environment.php");
$environment = new Environment(dirname(__DIR__));

//导入框架核心类
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

//实例化应用并执行
$application = new common\web\Application($environment->config);
$application->run();
