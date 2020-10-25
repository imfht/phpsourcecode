<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:20
 */

use fastwork\Container;

require __DIR__ . '/vendor/autoload.php';

define('APP_DEBUG', true);
// 检测swoole版本
if (version_compare(swoole_version(), '4.2.13', '<')) {
    exit("\e[41m swoole version is less than 4.2.13 \e[0m" . PHP_EOL);
}
// 应用初始化
$app_path = __DIR__ . '/app/';
$app = Container::get('fastwork')->setAppPath($app_path);
\Swoole\Runtime::enableCoroutine();
$app->run();