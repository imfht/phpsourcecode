<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_THEME'=>C('DEFAULT_THEME__MANAGE'),
	'TMPL_PARSE_STRING' =>  array( // 添加输出替换
			'__UPLOAD__'    =>  __ROOT__.'/Uploads',
			'__PUBLIC__' => __ROOT__. '/App/'.'Manage'.'/'.'View'.'/'.C('DEFAULT_THEME__MANAGE'),
	),
);