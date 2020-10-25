<?php
return [
    
	'template'=> [
    'view_suffix' => 'html',
	'view_depr'    => '_',
    ],
		'view_replace_str'  =>  [
				'__ROOT__' => WEB_URL,
				'__INDEX__' => WEB_URL . '/index.php',
				'__ADMIN__' => WEB_URL . '/public/admin',
				'__PUBLIC__' => WEB_URL . '/public',
				'__HOME__' => WEB_URL . '/template/',
				'__UPLOAD__' => '/uploads'
		
		],
	

		// 'dispatch_success_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
	//	 'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
		//默认错误跳转对应的模板文件
		'dispatch_error_tmpl' => 'public/tips',
		//默认成功跳转对应的模板文件
		'dispatch_success_tmpl' => 'public/tips',
		
		// 默认控制器名
		'default_controller'     => 'Index',
		// 默认操作名
		'default_action'         => 'adminindex',
		
];
