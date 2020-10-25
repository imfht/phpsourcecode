<?php
return array(

    'URL_HTML_SUFFIX'=>'',
    'SHOW_PAGE_TRACE' => false,
	'URL_MODEL'            => 3,
	'VIEW_PATH'	=>'./Themes/Admin/',
	 'TMPL_PARSE_STRING'=>array(   
	 '__PUBLIC__' => __ROOT__ . '/Common',
	'__RES__' => '/Themes/'.BIND_MODULE.'/Public', 
    '__IMG__'=>'./Themes/'.BIND_MODULE.'/Public/img',
    '__CSS__'=>'./Themes/'.BIND_MODULE.'/Public/css',
    '__JS__'=> './Themes/'.BIND_MODULE.'/Public/js',
    ), 
    
	'SESSION_PREFIX' => 'oscshop_admin_s', //session前缀
    'COOKIE_PREFIX'  => 'oscshop_admin_c', // Cookie前缀 避免冲突    
    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR' => './Themes/'.BIND_MODULE . '/Public/error.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => './Themes/'.BIND_MODULE . '/Public/success.html', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => './Themes/'.BIND_MODULE . '/Public/exception.html',// 异常页面的模板文件
    
    'LANG_SWITCH_ON' => true,
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST'        => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
   
);