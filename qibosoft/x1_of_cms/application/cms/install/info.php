<?php
return [
	'keywords'=>basename(dirname(__DIR__)),	//关键字,即是目录名也是数据表区分符
	'name'=>'CMS系统',	//模块名称
	'author'=>'齐博',	//开发者
	'author_url'=>'http://www.php168.com',	//开发者网站或者是演示网址
	'type'=>'1',		//当前模块是否可以复制
	'about'=>'',	//介绍
	'version'=>'1.0',	//版本号
	'icon'=>'fa fa-fw fa-file-text',		//CSS图片
	'ifsys'=>'0',	//是否禁止卸载	
	'config_group' =>['基础设置',],	//参数配置分组
	//涉及到的数据表,不要写前缀
	'sql_db' =>['content','content1','content2','content3','field','module','member','sort','category','info'],
];