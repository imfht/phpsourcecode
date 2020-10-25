<?php
/**
 * Printemps Framework - Tiny PHP Framework
 * ----------------------------------------------
 * Printemps Framework Config File
 * Printemps Framework 框架配置文件
 * Copyright(c) 2015 Printemps Framework DevTeam All rights reserved.
 * https://printemps.kotori.ovh/
 */

/** 运行常量定义，包括路径等信息 */

/* 定义框架位置相对路径，请一定要在开发过程中为每个文件加上一句 if(!defined('APP_ROOT_PATH')) exit; 防止非法运行=A= */
define("APP_ROOT_PATH", dirname(__file__));

/* 定义框架核心库（Core）相对路径，默认是 /Core/ =w= */
define('APP_CORE_PATH', APP_ROOT_PATH.'/Core/');

/* 定义框架用户界面（View）相对路径，默认是 /View/ */
define('APP_UI_PATH',APP_ROOT_PATH.'/View/');

/* 定义框架静态文件（Assets）相对路径，默认是 /Assets/ */
define('APP_STATIC_PATH', APP_ROOT_PATH.'/Assets/');

define('APP_NAME','Printemps Framework Dev Application');			//定义App的名称，可以直接调用常量输出
define('APP_VERSION', 'Alpha');						//定义App的版本，支持使用字母或版本号
define('APP_DEBUG_MODE', true);						//定义App是否处于调试环境，true or false，请注意关闭后程序不会报错，建议在生产模式关闭

/**
 * APP 入口模式：指定URL解析的模式
 * @var int
 * @default 1:QUERY_STRING 模式
 * @extends 2:PATH_INFO 模式（需要服务器支持PATHINFO，否则将会报错）
 * @premium 3:伪静态模式（需要配置好服务器的伪静态规则，否则会报404/500错误）
 */
define('APP_ENTRY_MODE',2);

/** 以下是对数据库的配置信息 */
$config = array(
	"dbHost" => "localhost",			//数据库主机，默认是 127.0.0.1 或者 localhost
	"dbUser" => "root",				//数据库用户名，默认是 root
	"dbPwd" => "",				//数据库密码，一般是 root 或者为空
	"dbPort" => 3306,				//数据库端口，一般是3306
	"dbName" => "moeframework",		//数据库名称
	"dbEncode" => "utf8",				//数据库整理，默认为 utf8
	"method" => "pdo",				//数据库查询方式，默认为 pdo ，次推荐为 mysqli 某些情况下主机不支持MySQLi拓展，请使用 mysql
	);

/** 修改上述信息即可开始开发/使用 ヾ(*´▽‘*)ﾉ Enjoy！ */

require APP_CORE_PATH.'Start.php';