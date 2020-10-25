<?php
//配置文件
return [
    // +----------------------------------------------------------------------
    // | 会话设置
    // +---------------------------------------------------------------------- 
    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'session_id',
        // SESSION 前缀
        'prefix'         => 'jingling_home',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => 'jingling_home_',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],
	// +----------------------------------------------------------------------
	// | 模板替换
	// +----------------------------------------------------------------------
	'view_replace_str'  =>  [
		'__PUBLIC__'=>__ROOT__.'/static', 
		'__STATIC__' => __ROOT__.'/static',
		'__ADDONS__' => __ROOT__.'/static/home/addons',
		'__IMG__'    =>__ROOT__.'/static/home/images',
		'__CSS__'    => __ROOT__.'/static/home/css',
		'__JS__'     => __ROOT__.'/static/home/js',
	],  
		// +----------------------------------------------------------------------
		// | 模板设置
		// +----------------------------------------------------------------------
		
		'template'               => [
				// 模板引擎类型 支持 php think 支持扩展
				'type'         => 'Think',
				// 模板路径
// 				'view_path'    => APP_PATH.'home/view/default/',
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
				// 预先加载的标签库
				'taglib_pre_load'     =>    'app\common\taglib\Think,app\common\taglib\Article',
		],
];
 
