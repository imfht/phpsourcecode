<?php

/**
 * Worker入口文件
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015-2016 ShuangYa
 */

require (__DIR__ . '/../framework/sy.php');

$config = __DIR__ . '/../application/config.php';

Sy::createConsoleApplication(__DIR__, $config);
