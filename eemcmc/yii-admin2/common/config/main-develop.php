<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=;dbname=',
			'username' => '',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => '',
			'emulatePrepare' => false,
			'attributes' => [
				PDO::ATTR_STRINGIFY_FETCHES => false,
			],
			'enableSchemaCache' => false,
			'schemaCacheDuration' => 3600,
		],
		'log' => [
			'targets' => [
				'file' => [
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning', 'trace', 'info'],
					'categories' => ['yii\*'],
				],
			],
		],
	],
	'params' => [
		'site_url' => 'http://yii-admin2.cn/',		
	]
];
