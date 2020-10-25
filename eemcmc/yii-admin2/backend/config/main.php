<?php

return [
	'id' => 'app-backend',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'backend\controllers',
	'bootstrap' => ['log'],
	'modules' => [],
	'components' => [
		'request' => [
			'enableCookieValidation' => false, //是否加密cookie
			'cookieValidationKey' => 'gbMc6V2HmsGO18jYlEWmGN16JU50gOm0',
		],
		'user' => [
			'identityClass' => 'backend\models\User',
			'enableAutoLogin' => true,
			'enableSession' => true,
		],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
			'defaultRoles' => ['guest'],
			'itemTable' => '{{%admin_auth_item}}',
			'itemChildTable' => '{{%admin_auth_item_child}}',
			'assignmentTable' => '{{%admin_auth_assignment}}',
			'ruleTable' => '{{%admin_auth_rule}}',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
	],
	'params' => [
		/**
		 * 权限配置
		 */
		'permissions' => [
			//站点权限
			1 => [
				'name' => '站点',
				'children' => [
					1 => ['name' => '首页', 'actions' => ['site/index']],
				]
			],
			//管理员管理
			6 => [
				'name' => '管理员管理',
				'children' => [
					1 => ['name' => '浏览管理员', 'actions' => ['systems/users/index', 'api/systems/users/index']],
					2 => ['name' => '修改管理员', 'actions' => ['api/systems/users/update']],
				]
			],
			//角色管理
			7 => [
				'name' => '角色管理',
				'children' => [
					1 => ['name' => '浏览角色', 'actions' => ['systems/roles/index', 'api/systems/roles/index']],
					2 => ['name' => '修改角色', 'actions' => ['api/systems/roles/update']],
					3 => ['name' => '权限维护', 'actions' => ['systems/roles/permissions', 'api/systems/roles/save-permissions']],
					4 => ['name' => '成员维护', 'actions' => ['api/systems/roles/users']],
				]
			],
			//操作日志管理
			9 => [
				'name' => '操作日志管理',
				'children' => [
					1 => ['name' => '浏览日志', 'actions' => ['systems/historys/index', 'api/systems/historys/index']],
				]
			],
			//版本发布管理
			10 => [
				'name' => '版本发布管理',
				'children' => [
					1 => ['name' => '浏览版本', 'actions' => ['systems/releases/index', 'api/systems/releases/index']],
					2 => ['name' => '修改版本', 'actions' => ['api/systems/releases/update']],
					3 => ['name' => '删除版本', 'actions' => ['api/systems/releases/delete']],
				]
			],
			//用户管理
			11 => [
				'name' => '用户管理',
				'children' => [
					1 => ['name' => '浏览用户', 'actions' => ['buyers/index', 'api/buyers/index']],
					2 => ['name' => '修改用户', 'actions' => ['api/buyers/update']],
				]
			],
		],
		//菜单配置
		//url表示用于链接和样式控制
		//action表示对应的权限动作，是否显示
		'menus' => [
			
			[
				'name' => '系统设置',
				'icon' => 'icon-cog',
				'url' => 'systems',
				'children' => [
					['icon' => 'icon-user', 'name' => '管理员管理', 'url' => 'systems/users', 'action' => 'systems/users/index'],
					['icon' => 'icon-group', 'name' => '角色管理', 'url' => 'systems/roles', 'action' => 'systems/roles/index'],
					['icon' => 'icon-time', 'name' => '操作日志', 'url' => 'systems/historys', 'action' => 'systems/logs/index'],
					['icon' => 'icon-share', 'name' => '版本发布管理', 'url' => 'systems/releases', 'action' => 'systems/releases/index'],
				]
			],
		],
	],
];
