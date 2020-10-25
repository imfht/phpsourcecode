<?php

/**
 * 系统配置文件
 */
return array(
	'DB_TYPE'			 => 'mysql',
	'DB_HOST'			 => 'localhost',
	'DB_NAME'			 => 'cmf_cxphp',
	'DB_USER'			 => 'root',
	'DB_PWD'			 => '',
	'DB_PORT'			 => '3306',
	'DB_PREFIX'			 => 'sp_',
	/* Default Module */
	'DEFAULT_MODULE'	 => 'Portal',
	/* Data Auth Key */
	"DATA_AUTH_KEY"		 => 'EHOsLfKKnGWwt7UtMG',
	/* cookies Prefix */
	"COOKIE_PREFIX"		 => 'Ob8M4q_',
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
