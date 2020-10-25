<?php
/**
 * 入口文件
 */
define('ROOT_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('IN_TEMPLI',true);

//项目url
define('_PHP_FILE_',rtrim($_SERVER['PHP_SELF'],'/'));
$_root = dirname(_PHP_FILE_);
//网站根url  不包含 http://www.templi.cc
define('APP_URL',($_root=='/' || $_root=='\\')?'':$_root);

require '../Templi.php';
$config = require ROOT_PATH.'config/config.php';
(new Templi($config))->run();