<?php
return array (
		// '配置项'=>'配置值'
		//'SHOW_PAGE_TRACE' => true,
		'USER_AUTH_ON' => true,
		'USER_AUTH_TYPE' =>2,		// 默认认证类型 1 登录认证 2 实时认证
		'ADMIN_AUTH_KEY'=>'krasen',
		'USER_AUTH_GATEWAY' => 'Index/login',
		'NOT_AUTH_MODULE' => 'Index',
		'USER_AUTH_MODEL' => 'userinfo',
		'RBAC_ROLE_TABLE'=>'sc_role',
		'RBAC_USER_TABLE'=>'sc_role_user',
		'RBAC_ACCESS_TABLE'=>'sc_access',
		'RBAC_NODE_TABLE'=>'sc_node',
		'USER_AUTH_KEY'=>'authId',
		'DB_HOST' => 'localhost',
		'DB_USER' => 'root',
		'DB_PWD' => 'krasen',
		'DB_NAME' => 'webinfo',
		'DB_PORT' => '3306',
		'DB_PREFIX' => 'sc_',
		'URL_MODEL' => 0,
		'TMPL_PARSE_STRING' => array (
				//'__IMG__' => 'Common/images',
				//'__JS__' => 'Common/js',
				//'__CSS__' => 'Common/css',
				//'__AIMG__'=>'Common/admin/images',
				//'__AJS__'=>'Common/admin/js',
				//'__ACSS__'=>'Common/admin/css'
		) 
);
?>