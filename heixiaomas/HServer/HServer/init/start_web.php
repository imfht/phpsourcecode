<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/9/12
 * Time: 18:00
 */

require_once __DIR__ . "/../HWebServer.php";

require_once __DIR__ . '/../load.php';

use Workerman\Worker;
use HServer\HWebServer;

$web = new HWebServer("http://0.0.0.0:8800");
$web->name = "HServer->WEB服务器";
$web->count = 4;

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
