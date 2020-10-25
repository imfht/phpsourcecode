<?php
header('Content-Type:text/html;charset=utf-8');

define('ROOT_PATH',dirname(dirname(__FILE__)));
define('TEMP_DIR',ROOT_PATH.'/templates/');
define('COMP_DIR',ROOT_PATH.'/compiles/');
define('CACHE_DIR',ROOT_PATH.'/cache/');
define('IS_CACHE',true);

require ROOT_PATH.'/includes/Templates.class.php';
$tpl = new Templates();
IS_CACHE ? ob_start() : null;
?>