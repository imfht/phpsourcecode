<?php defined('SYSPATH') OR die('No direct access allowed.');
//织梦数据库配置文件载入，如果配置文件路径有变化请修改路径，也可以直接填写对应的数据
if(file_exists(DOCROOT.'../../data/common.inc.php'))
	require DOCROOT.'../../data/common.inc.php';
return array
(
	'default' => array
	(
		'type'       => 'MySQL',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 * array    variables    system variables as "key => value" pairs
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'   => $cfg_dbhost ? $cfg_dbhost : 'localhost',
			'database'   => $cfg_dbname ? $cfg_dbname : 'kohana',
			'username'   => $cfg_dbuser ? $cfg_dbuser : FALSE,
			'password'   => $cfg_dbpwd ? $cfg_dbpwd : FALSE,
			'persistent' => FALSE,
		),
		'table_prefix' => $cfg_dbprefix ? $cfg_dbprefix : '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
	),
	'alternate' => array(
		'type'       => 'PDO',
		'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn         Data Source Name
			 * string   username    database username
			 * string   password    database password
			 * boolean  persistent  use persistent connections?
			 */
			'dsn'        => 'mysql:host=localhost;dbname=kohana',
			'username'   => 'root',
			'password'   => 'r00tdb',
			'persistent' => FALSE,
		),
		/**
		 * The following extra options are available for PDO:
		 *
		 * string   identifier  set the escaping identifier
		 */
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
	),
);
