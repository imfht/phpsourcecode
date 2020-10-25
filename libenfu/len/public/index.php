<?php
/**
 * Created by PhpStorm.
 * Author: Len
 * mail: i@91coder.org
 * Date: 2017/6/3
 * Time: 0:49
 * Slogan : PHP the best language in the world
 */

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.5.0', '<')) die('require PHP > 5.5.0 !');

define('DEBUG', true);
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR);

require ROOT_DIR . 'Len/Len.php';