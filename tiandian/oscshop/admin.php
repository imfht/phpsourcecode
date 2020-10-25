<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

header("Content-Type:text/html; charset=utf-8");    

define('APP_DEBUG', true);

define('BIND_MODULE','Admin');
  
define ('APP_PATH', './Modules/' );

if (!is_file( 'Modules/Common/Conf/db.php')) {
    header('Location: ./install.php');
    exit;
}

define('ROOT_PATH',str_replace('\\','/',dirname(__FILE__)) . '/'); 

define ('RUNTIME_PATH','./Runtime/');

require './ThinkPHP/ThinkPHP.php';