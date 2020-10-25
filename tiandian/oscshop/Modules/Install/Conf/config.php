<?php
/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(

    'ORIGINAL_TABLE_PREFIX' => 'oscshop_', //默认表前缀
	
	'DEFAULT_THEME'			 => 'default', 
	'TMPL_TEMPLATE_SUFFIX'	 => '.html', 
	'VIEW_PATH'				 => './Themes/Install/',
	'TMPL_PARSE_STRING'=>array(   
	'__PUBLIC__' => __ROOT__ . '/Common',
	'__RES__' => __ROOT__.'/Themes/'.MODULE_NAME.'/default/Public', 
    '__IMG__'=>__ROOT__.'/Themes/'.MODULE_NAME.'/default/Public/images',
    '__CSS__'=>__ROOT__.'/Themes/'.MODULE_NAME.'/default/Public/css',
    '__JS__'=> __ROOT__.'/Themes/'.MODULE_NAME.'/default/Public/js',
    '__NAME__'=>'OscShop',
    '__COMPANY__'=>' 李梓钿 ',
    '__WEBSITE__'=>'www.oscshop.cn',
    '__COMPANY_WEBSITE__'=>'www.oscshop.cn'
    ), 
	
    /* URL配置 */
    'URL_MODEL' => 3, //URL模式
    'DEFAULT_THEME' =>  'default',  // 默认模板主题名称
    'SESSION_PREFIX' => 'oscshop', //session前缀
    'COOKIE_PREFIX' => 'oscshop_', // Cookie前缀 避免冲突

);