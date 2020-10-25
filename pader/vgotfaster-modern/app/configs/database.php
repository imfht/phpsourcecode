<?php
/*
	VgotFaster PHP Framework
	Database Config
		You can set config ['port'] to set connect port
*/

$database['default_config'] = 'default';
$database['use_pdo_driver'] = true;

$database['default'] = array(
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '0000',
	'database' => 'test',
	'tbprefix' => '',
	'pconnect' => FALSE,
	'charset'  => 'gbk',
	'dbcollat' => 'gbk_chinese_ci',
	'dbdriver' => 'mysql',
	'debug'    => TRUE
);

$database['ucenter'] = array(
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '0000',
	'database' => 'ucenter',
	'tbprefix' => 'uc_',
	'pconnect' => FALSE,
	'charset'  => 'gbk',
	'dbcollat' => 'gbk_chinese_ci',
	'dbdriver' => 'mysql',
	'debug'    => TRUE
);

$database['sqlite'] = array(
	'filename' => 'app/data/sqlite.db',
	'tbprefix' => '',
	'dbdriver' => 'sqlite',
	'debug'    => TRUE
);
