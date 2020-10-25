<?php
/**
 * Created by PhpStorm.
 * User: Epii
 * Date: 2019/9/12
 * Time: 10:51 AM
 *
 * 这个文件可以改动。之后的更新 使用 composer update
 *
 */
require_once __DIR__ . "/../vendor/autoload.php";

\epii\server\App::getInstance()->init(\init\example::class)->setBaseNameSpace("app\\api")->run();