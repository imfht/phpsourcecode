<?php

/**
 * 系统配置文件
 */
return array(
	'DB_TYPE'			 => 'mysql',
	'DB_HOST'			 => '#DB_HOST#',
	'DB_NAME'			 => '#DB_NAME#',
	'DB_USER'			 => '#DB_USER#',
	'DB_PWD'			 => '#DB_PWD#',
	'DB_PORT'			 => '#DB_PORT#',
	'DB_PREFIX'			 => '#DB_PREFIX#',
	/* Default Module */
	'DEFAULT_MODULE'	 => 'Portal',
	/* Data Auth Key */
	"DATA_AUTH_KEY"		 => '#AUTHCODE#',
	/* cookies Prefix */
	"COOKIE_PREFIX"		 => '#COOKIE_PREFIX#',
	/* Potal Tpl Path */
	'CMF_TPL_PATH'		 => CMF_ROOT . '/static/Portal/',
	/* CMF Config Path */
	'CMF_CONF_PATH'		 => CMF_DATA . '/config/',
	/* CMF Databack Path */
	'CMF_DATA_PATH'		 => CMF_DATA . '/backup/',
	/* TMPL Parse String  */
	'TMPL_PARSE_STRING'	 => array(
		'__TMPL__' => __ROOT__ . '/static/Portal/default',
	),
);
