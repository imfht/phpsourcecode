<?php
 return array (

	'URLMode'   => 0,			
	'ActionDir' => 'hiddenDir/',
	'htmlExt'  => '.html',
	'ReWrite'  => true,
	'Router'  => '',
	'Debug'     => true,  
	'Session'   => true,
	'pageSize'  =>10,
	'DB'=>array(
		'Persistent'=>false,
		'DBtype'    => 'Mysql',
		'DBcharSet' => '===db_charset===',
		'DBhost'    => '===db_host===',
		'DBport'    => '===db_port===',
		'DBuser'    => '===db_user===',
		'DBpsw'     => '===db_pwd===',
		'DBname'    => '===db_name==='
	),
	'setSmarty'=>array(
		'template_dir'    => VIEW.'template',
		'compile_dir'     => _mkdir(CACHE. 'c_templates'),
		'left_delimiter'  => '#{',
		'right_delimiter' => '}#',
	),	
); 
?>