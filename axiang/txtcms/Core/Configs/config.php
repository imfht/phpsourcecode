<?php
/**
 * TXTCMS 项目配置
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-28
 */
defined('INI_TXTCMS') or exit();
$config = require TEMP_PATH.'config.php';
$array = array(
	'ROBOT_FILE'=>LOG_PATH.'robots.log',
	'DEFAULT_GROUP'=>'Home',
	'APP_GROUP_LIST'=>'Home,Admin,Plus',	 //项目分组，多个用逗号隔开
	'DB_HASH_LIST'=>'arcbody',	 //分表名，多个用逗号隔开
	'DEFAULT_THEME'=>$config['web_default_theme'],
	'URL_MODEL'=>$config['web_url_model'],
	'URL_PATH_DEPR'=>$config['web_path_depr'],
	'URL_PATH_SUFFIX'=>$config['web_path_suffix'],
	'HTML_CACHE' =>$config['web_caching'],
	'URL_ROUTER_ON' =>$config['web_url_route_on'],  //开启路由
	'URL_ROUTE_RULES' => 
	  array (
		'/^'.$config['web_url_route']['list'].'$/' => 'Home/Article/lists?id=:1',
		'/^'.$config['web_url_route']['list_p'].'$/' => 'Home/Article/lists?id=:1&p=:2',
		'/^'.$config['web_url_route']['show'].'$/' => 'Home/Article/show?id=:1',
		'/^'.$config['web_url_route']['show_p'].'$/' => 'Home/Article/show?id=:1&p=:2',
	  ),
);
return array_merge($config,$array);