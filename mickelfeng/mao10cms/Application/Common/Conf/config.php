<?php
return array(
	'LOAD_EXT_CONFIG' => 'db',
	'DEFAULT_MODULE'     => 'Home', //默认模块
    'URL_MODEL'          => '0', //URL模式
    'DEFAULT_FILTER' => 'htmlspecialchars',//htmlspecialchars过滤
    'SESSION_AUTO_START' => true, //是否开启session
	'URL_PATHINFO_DEPR'=>'-', //更改PATHINFO参数分隔符
	'TMPL_TEMPLATE_SUFFIX' => '.php', //更改模板文件后缀
	'TMPL_ENGINE_TYPE' => 'PHP', //自定义模板引擎
	'DEFAULT_THEME' => 'default', //开支多模板支持，设置默认模板目录为default
	'TMPL_LOAD_DEFAULTTHEME' => true, //开启差异主题定义方式，当前模板无对应文件时，会自动调用默认模板文件
	'LOG_RECORD' => true, //关闭日志
	'LOG_LEVEL' =>'WARN',
	'VIEW_PATH' => THINK_PATH.'../Theme/', //指定模板目录
	'URL_ROUTER_ON'   => true, 
	'URL_ROUTE_RULES'=>array(
	    '/^(\d+)$/' => 'Index/single?id=:1',
	),
);