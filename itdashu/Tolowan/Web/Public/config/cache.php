<?php
$settings = array(
	'all' => array(
		'name' => '清除所有缓存',
		'description' => '清空cache目录',
		'href' => array(
			'for' => 'adminCache',
			'type' => 'all',
			'handle' => 'clear',
		),
	),
	'volt' => array(
		'name' => '清除模板缓存',
		'description' => '清空cache目录',
		'href' => array(
			'for' => 'adminCache',
			'type' => 'volt',
			'handle' => 'clear',
		),
	),
	'config' => array(
		'name' => '清除配置缓存',
		'description' => '清空cache目录',
		'href' => array(
			'for' => 'adminCache',
			'type' => 'config',
			'handle' => 'clear',
		),
	),
	'download' => array(
		'name' => '清空下载临时目录',
		'description' => '清空cache目录',
		'href' => array(
			'for' => 'adminCache',
			'type' => 'download',
			'handle' => 'clear',
		),
	),
	'options' => array(
		'name' => '清除options缓存',
		'description' => '清空cache目录',
		'href' => array(
			'for' => 'adminCache',
			'type' => 'options',
			'handle' => 'clear',
		),
	),
);