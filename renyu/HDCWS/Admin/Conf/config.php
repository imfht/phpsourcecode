<?php

$config = include_once('../Config/config.php');

$systemConfig = include_once ('./Conf/config.system.php');

return array_merge($config, $systemConfig, array(

	'LOAD_EXT_CONFIG' => 'config.system',//加载扩展配置文件

	'TMPL_FILE_DEPR' => '_', // 控制器_方法.html, 控制器/方法.html[默认]

	'TMPL_TEMPLATE_SUFFIX' => '.html',//模板后缀
	
	'URL_MODEL' => 2,

	//去掉伪静态后缀
	'URL_HTML_SUFFIX' => '',

	'TMPL_PARSE_STRING' => array(

		'__AP__' =>  __ROOT__ . '/' . APP_NAME . '/Tpl/app',

		'__EXTJS__' => __ROOT__ . '/' . APP_NAME . '/Tpl/extjs'

	),

	//桌面背景图片上传地址
	'UPLOAD_Desktop_Dir' => './uploads/desktop'

));
