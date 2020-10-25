<?php
return [	
	
	//默认错误跳转对应的模板文件
	'dispatch_error_tmpl' => APP_PATH.'mobile/view/public/error.tpl',
	//默认成功跳转对应的模板文件
	'dispatch_success_tmpl' => APP_PATH.'mobile/view/public/success.tpl',		
		
	'captcha'=>[
		'useNoise'=>false,
		'length'=>4,
		'fontSize'=>18,
		'imageH'=>'53',
		'imageW'=>'130'
	],
	'template'=> [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 视图基础目录，配置目录为所有模块的视图起始目录
        'view_base'    => '',
        // 当前模板的视图目录 留空为自动获取
        'view_path'    => './themes/'.THEMES.'/'.request()->module().'/',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],
];
