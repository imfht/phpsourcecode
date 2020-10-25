<?php

/**
 * Dayrui Website Management System
 * 
 * @since			version 2.0.0
 * @author			Dayrui <dayrui@gmail.com>
 * @license     	http://www.dayrui.com/license
 * @copyright		Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * 默认路由配置（不允许更改）
 */
 
$route['test']					= 'api/test';
$route['sitemap.xml']			= 'api/sitemap';
$route['search-(.*)\.html']		= 'search/index/rewrite/$1';
$route['404_override']			= '';
$route['default_controller']	= 'home';

if (is_file(APPPATH.'config/rewrite.php')) require APPPATH.'config/rewrite.php';

/**
 * 自定义路由
 */
 
//$route['自定义路由正则规则']	= '指向的路由URI（必须是v2的URI规则：控制器/方法/参数1/参数1的值/参数2/参数2的值...）';

