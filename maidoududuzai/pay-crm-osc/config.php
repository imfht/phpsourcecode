<?php

if(!defined('_ROOT_')) {
	exit();
}

use \think\Config;

$db = [
	'host' => 'localhost',
	'port' => '3306',
	'name' => 'dev_tryyun',
	'user' => 'dev_tryyun',
	'passwd' => 'dev_tryyun',
	'prefix' => 'befen_',
];

Config::set('app_debug', true);

Config::set('is_https', true);
Config::set('url_common_param', true);

Config::set('dispatch_error_tmpl', 'data/msg.htm');
Config::set('dispatch_success_tmpl', 'data/msg.htm');

