<?php
return array(
	'URL_CASE_INSENSITIVE' => true,//url地址区分大小写，true为不区分
	'LOAD_EXT_CONFIG' => 'conn,appcfg',//自动加载其他配置文件
	'LOAD_EXT_FILE' => 'firstpinyin,pinyin,arraysort,arrayiconv,randnum,curl',//自动加载函数
	'ADMIN_IMG' => 'Skin/Admin/Img',
	'TMPL_PARSE_STRING'  => array(
		 '__ADMIN.JS__' => 'Skin/Admin/Js',
		 '__ADMIN.IMG__' => 'Skin/Admin/Img',
		 '__ADMIN.CSS__' => 'Skin/Admin/Css',
		 '__ADMIN.UPLOAD__' => 'Uploads/Admin',
		 '__UI__' => 'Skin/Public/Easyui', 
		 '__JS__' => 'Skin/Public/Js',
		 '__IMG__' => 'Skin/Public/Img',
		 '__CSS__' => 'Skin/Public/Css', 
		 '__UPLOAD__' => 'Uploads', 
		 '__RUNTIME__' => 'Runtime/',
	),
	//'配置项' => '配置值'
);
?>