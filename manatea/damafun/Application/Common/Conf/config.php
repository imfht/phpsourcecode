<?php
return array(
	//'配置项'=>'配置值'
		'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
		'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件

	//使用smarty模板
	'TMPL_ENGINE_TYPE'      =>  'smarty',  
	'SHOW_PAGE_TRACE' =>true,
	'TMPL_ENGINE_CONFIG' =>array(
		'left_delimiter' => '<{',
		'right_delimiter' => '}>',	
	),	
	'TMPL_TEMPLATE_SUFFIX'  =>  '.tpl',     // 默认模板文件后缀
		'DB_TYPE'   => 'mysql', // 数据库类型
		'DB_HOST'   => '127.0.0.1', // 服务器地址
		'DB_NAME'   => 'db_oldama1', // 数据库名
		'DB_USER'   => 'root', // 用户名
		'DB_PWD'    => '19921221', // 密码
		'DB_PORT'   => 3306, // 端口
		'DB_PREFIX' => 'cz_', // 数据库表前缀
		'DB_CHARSET'=> 'utf8', // 字符集
		'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
);