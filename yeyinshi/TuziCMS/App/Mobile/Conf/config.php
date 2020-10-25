<?php
return array(
	//'配置项'=>'配置值'
		'HTML_CACHE_ON' => C('HTML_CACHE_ON__MOBILE'),
		'HTML_CACHE_RULES'=>array(
				'index:index'=>array('Mobile/{:module}_{:action}_index',C('HTML_TIME_INDEX__HOME')),
				//'index'=>array('Mobile/{:module}_{:action}',C('HTML_TIME_INDEX__MOBILE')),
				'group'=>array('Mobile/{:module}_{:action}_{id}_{p|intval}',C('HTML_TIME_INDEX__MOBILE')),
				'detail'=>array('Mobile/{:module}_{:action}_{id}_{p|intval}',C('HTML_TIME_INDEX__MOBILE')),
		
		),
		'DEFAULT_THEME'=>C('DEFAULT_THEME__MOBILE'),
		'TMPL_PARSE_STRING' =>  array( // 添加输出替换
				'__UPLOAD__'    =>  __ROOT__.'/Uploads/Images',
				'__PUBLIC__' => __ROOT__. '/Public/'.'Mobile'.'/'.C('DEFAULT_THEME__MOBILE'),
		),
		
		'VIEW_PATH'=>'./Public/Mobile/', //改变某个模块的模板文件目录
);