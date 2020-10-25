<?php
return [
	'keywords'  =>basename(dirname(__DIR__)),  //关键字,即是目录名也是数据表区分符
	'name'      =>'评论',  //模块名称
	'author'    =>'龙城',  //开发者
	'author_url'=>'https://x1.php168.com/',  //开发者网站或者是演示网址
	'type'      =>'0',    //当前模块是否可以复制
	'about'     =>'系统内置的评论 卸载后可以重新安装',  //介绍
	'version'   =>'1.0',  //版本号
	'icon'      =>'fa fa-fw fa-bullhorn',    //CSS图片
	'ifsys'     =>'0',  //是否禁止卸载
	'sql_db'    =>['comment_content'],
	'config_group'=>['评论设置'],
];

 