<?php
 /**
 +------------------------------------------------------------------------------
 * Framk PHP框架
 +------------------------------------------------------------------------------
 * @package  Framk
 * @author   shawn fon <shawn.fon@gmail.com>
 +------------------------------------------------------------------------------
 */
//系统默认配置 
 return array (

	'URLMode'   => 0,			
	'ActionDir' => '',
	
	'htmlExt' => '.html',
	'ReWrite' => false,
	'Debug'   => false,  
	'Session' => true,
	
	'DB'=>array(
	
		'Persistent' =>false,
		'DBtype'    => 'Mysql',
		'DBcharSet'  => 'utf8',
		'DBhost'    => 'localhost',
		'DBport'    => '3306',
		'DBuser'    => 'root',
		'DBpsw'     => 'root',
		'DBname'    => 'sq_framkdb',
	
	),
	
	'setSmarty'=>array(
		'template_dir'    => VIEW.'templates',
		'compile_dir'     => _mkdir(CACHE. 'c_templates'),
		'left_delimiter'  => '{',
		'right_delimiter' => '}',
	),
	
); 

  ?>
