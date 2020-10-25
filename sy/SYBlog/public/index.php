<?php

/**
 * 入口文件
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

require (__DIR__ . '/../framework/sy.php');

$config = __DIR__ . '/../application/config.php';

Sy::createApplication(__DIR__, $config);
