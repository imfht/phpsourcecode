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

@error_reporting(E_ALL &~ E_NOTICE);

require (__DIR__ . '/BaseSY.php');

class Sy extends \sy\BaseSY {
}

spl_autoload_register(['Sy', 'autoload'], TRUE, TRUE);
