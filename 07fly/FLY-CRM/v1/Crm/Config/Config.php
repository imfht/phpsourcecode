<?php
/**
 * @CopyRight  (C)2006-2017 07fly Development team Inc.
 * @WebSite    www.07fly.com www.07fly.top
 * @Author     07fly.com <web@07fly.com>
 * @Brief      07flyCRM v1.x
 * @Update     2016.06.11
 * @author:    kfrs
**/
//用户配置
 return array (

	'URLMode'   => 0,			
	'ActionDir' => 'hiddenDir/',
	'htmlExt'   => '.html',
	'ReWrite'   => false,
	'Debug'     => false,  
	'Session'   => true,
	'pageSize'  =>20,
	'xml'=>array(
		'path'=>EXTEND.'xml',
		'root'=>'niaomuniao',
	),	
	'DB'=>array(
	'Persistent'=>false,
	'DBtype'    => 'Mysql',
	'DBcharSet' => 'utf8',
	'DBhost'    => 'localhost',
	'DBport'    => '3306',
	'DBuser'    => 'root',
	'DBpsw'     => 'root',
	'DBname'    => '07fly_crm'
	),
	
	'setSmarty'=>array(
		'template_dir'    => VIEW.'templates',
		'compile_dir'     => _mkdir(CACHE. 'templates_c'),
		'left_delimiter'  => '#{',
		'right_delimiter' => '}#',
	),
); 
?>