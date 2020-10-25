<?php

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
return [
	'id' => 'yii-admin2',
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor', //包目录
	'timeZone' => 'Asia/Chongqing', //时区
	'language' => 'zh-CN', //语言包
	'components' => [
		//数据库配置
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
		//会话配置
		'session' => [
			'class' => 'yii\web\CacheSession',
		],
		//缓存配置
		'cache' => [
			//'class' => 'yii\caching\DummyCache',
			'class' => 'yii\caching\FileCache',
			'cachePath' => '@common/runtime/cache',
		],
		//路由配置
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
		//授权配置
		'authManager' => [
			'class' => 'yii\rbac\PhpManager',
		],
		//邮件配置
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
			'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
                'host' => 'smtpcloud.xxxx.com',
                'username' => 'xxxx',
                'password' => 'aaaaa',
                'port' => '25',
			],
		],
		//登录用户对象配置
		'user' => [
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
			'enableSession' => true,
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning', 'trace', 'info'],
					// 'levels' => ['error', 'warning'],
				//'categories' => ['yii\*'],
				],
			],
		],
	],
	//应用参数配置
	'params' => [
		'email' => 'service@yii-admin2.cn',
		'user.passwordResetTokenExpire' => 3600,
		'user.remember_expire' => 3600 * 24 * 30 * 12, //记住用户登录的超时时间, 一年
		'site_url' => 'http://yii-admin2.cn/',
	]
];
