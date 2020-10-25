<?php
defined('INPOP') or exit('Access Denied');

$_config['db']['host'] = "192.168.1.13:3306";
$_config['db']['user'] = "1c36601d-356e";
$_config['db']['password'] = "6ef6fcfc-22ef";
$_config['db']['dbname'] = "d4142643c8a814719952b2f26376175cb";
$_config['db']['dbcharset'] = 'utf8';
$_config['db']['tablepre'] = '';
$_config['cookie']['pre'] = 'pop_';
$_config['cookie']['path'] = '/';
$_config['cookie']['domain'] = '';
$_config['sys']['rewrite']['enable'] = 0;
$_config['sys']['pagesize'] = 100;
$_config['sys']['cachepath'] = LIB_PATH.DS.'cache';
$_config['sys']['upload']['format'] = 'jpg|jpeg|gif|bmp|png|doc|txt|rar|zip|htm|html|csv|pdf';
$_config['sys']['default_end'] = 'frontend';
$_config['sys']['default_control'] = 'do';
$_config['sys']['default_action'] = 'dashboard';
$_config['sys']['data_table_pre'] = 'data_';
$_config['sys']['data_table_init_sql'] = "CREATE TABLE `data_table_init_name` ( `data_table_id` INT(11) NOT NULL AUTO_INCREMENT,`workflowid` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (`data_table_id`)) DEFAULT CHARSET=utf8";
$_config['sys']['default_log'] = 'pop';
$_config['sys']['log_mode'] = 'mysql';
$_config['sys']['db_session'] = 0;
?>