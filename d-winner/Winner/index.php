<?php
/*
 * @varsion		Dream项目管理系统 1.1var
 * @package		程序设计由梦赢科技设计开发
 * @copyright	Copyright (c) 2010 - 2014, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

if(!file_exists(dirname(__FILE__).'/Conf/conn.php')){
    header('Location:Install/index.php');
}else{
	define('APP_DEBUG',true);
	require('Sys/ThinkPHP/ThinkPHP.php');
}
