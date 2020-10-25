<?php

// 配置文件

return array(

	'title'	=> 'gitman-web@lianzh',

	'template_dir' => __DIR__ . '/templates',

	// adminer 相关参数
	'adminer'	=> array(
		
		// 白名单设置
		'authorize'	=> array(
			'id'	=> 'authorizeId',
			'val'	=> 'gitmyweb001'
		),
		
	),

	// r-consume 相关参数
	'r-consume'	=> array(
		
		// endpoint
		'endpoint'	=> 'http://localhost/adminer.php?authorizeId=gitmyweb001',

		// 分支集合
		'deploy_ids'	=> "",// 多个id以 | 分隔		
	),

	// 数据源配置信息
	'dsn'	=> array(
		'type' => 'mysql',

		'dbpath'  => 'mysql:host=127.0.0.1;port=3306;dbname=gitman_web',
		'login'	=> 'root',
		'password' => 'root',

		'initcmd' => array(
				"SET NAMES 'utf8'",
			),

		'attr'	=> array(
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_PERSISTENT => false,
			),
	),

);