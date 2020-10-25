<?php
return [

	'template'=> [
    'view_path'    => './template/'.config('web.WEB_TPL').'/',
    'view_suffix' => 'html',
	'view_depr'    => '/',
    ],
		'view_replace_str'  =>  [
				'__ROOT__' => WEB_URL,
				'__INDEX__' => WEB_URL . '/index.php',
				'__HOME__' => WEB_URL . '/template/'.config('web.WEB_TPL').'/res',
				'__JS__' => WEB_URL . '/template/'.config('web.WEB_TPL').'/res/js',
				'__CSS__' => WEB_URL . '/template/'.config('web.WEB_TPL').'/res/css',
				'__IMG__' => WEB_URL . '/template/'.config('web.WEB_TPL').'/res/images',
				'__UPLOAD__' => WEB_URL . '/uploads',
				'__PUBLIC__' =>WEB_URL. '/public',
				'__PUBLIC_IMG__' =>WEB_URL. '/public/images',
			
		],
		//默认错误跳转对应的模板文件
		'dispatch_error_tmpl' => 'public/error',
		//默认成功跳转对应的模板文件
		'dispatch_success_tmpl' => 'public/error',
		//自定义默认主题设置
		'theme' =>[
				'pc' =>'PC',
				'mobile'=>'PC',
		],
];
