<?php 
return array(
	# route
	'route' => array(
		'mode' => 'default',
		'controller_flag' => 'c',
		'action_flag' => 'a',	
		),
	# template
	'smarty' => array(
		'left_delimiter' => "{{",
		'right_delimiter'   => "}}",
		'template_dir' => APP_PATH . '/view/',
		'compile_dir' => RUNTIME_PATH . '/template/template_c/',
		'cache_dir' => RUNTIME_PATH . '/template/template_d/',
		),
	'cookie' => array(
		'prev' => 'ysf_',
		'domain' => '',
		'path'	=> '/'
		),
	'upload' => array(
		'allow_ext' => array(
			'jpg','jpeg','gif','png','bmp'
			),
		'filesize' => 1024*1024*5,
		'savepath' => '/data/upload',
		),
	'timezone' => 'Asia/Chongqing',
	'authcode' => '6o9P0kQ7yUoiffP4H765aJi9wBb9nheZ',
);