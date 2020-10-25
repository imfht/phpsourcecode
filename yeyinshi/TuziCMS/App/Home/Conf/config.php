<?php
return array(
	//'配置项'=>'配置值'
		'HTML_CACHE_ON' => C('HTML_CACHE_ON__HOME'),
		'HTML_CACHE_RULES'=>array(
				'index:index'=>array('Home/{:module}_{:action}_index',C('HTML_TIME_INDEX__HOME')),
				
				'group'=>array('Home/{:module}_{:action}_{id}_{p|intval}',C('HTML_TIME_GROUP__HOME')),
				'detail'=>array('Home/{:module}_{:action}_{id}_{p|intval}',C('HTML_TIME_DETAIL__HOME')),
		
		),
		'DEFAULT_THEME'=>C('DEFAULT_THEME__HOME'),
		'TMPL_PARSE_STRING' =>  array( // 添加输出替换
				'__UPLOAD__'    =>  __ROOT__.'/Uploads',
				'__PUBLIC__' => __ROOT__. '/Public/'.'Home'.'/'.C('DEFAULT_THEME__HOME'),
		),
		
		'VIEW_PATH'=>'./Public/Home/', //改变某个模块的模板文件目录
);