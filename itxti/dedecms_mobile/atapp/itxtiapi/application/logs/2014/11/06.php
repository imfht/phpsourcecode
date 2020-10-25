<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-11-06 10:06:08 --- CRITICAL: Kohana_Exception [ 0 ]: A valid cookie salt is required. Please set Cookie::$salt in your bootstrap.php. For more information check the documentation ~ SYSPATH\classes\Kohana\Cookie.php [ 151 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Cookie.php:67
2014-11-06 10:06:08 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Cookie.php(67): Kohana_Cookie::salt('pgv_pvi', NULL)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(151): Kohana_Cookie::get('pgv_pvi')
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(117): Kohana_Request::factory(true, Array, false)
#3 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Cookie.php:67