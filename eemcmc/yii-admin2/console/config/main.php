<?php

return [
	'id' => 'app-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log', 'gii'],
	'controllerNamespace' => 'console\controllers',
	'modules' => [
		'gii' => 'yii\gii\Module',
	],
	'controllerMap' => [
		'migrate' => [
			'class' => 'yii\console\controllers\MigrateController',
			'migrationTable' => 'le_migrates',
		],
	],
];
