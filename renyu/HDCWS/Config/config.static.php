<?php

return array(

	//系统默认导航
	'HD_Static_Nav' => array(

		array('name' => '首页', 'url' => 'Index/index'),

		array('name' => '文章中心', 'url' => 'Article/index'),
		
		array('name' => '公司团队', 'url' => 'Article/lists?tid=2'),
		
		array('name' => '关于公司', 'url' => 'Article/lists?tid=3'),

		array('name' => '产品中心', 'url' => 'Product/index'),
		
		array('name' => '访客留言', 'url' => 'Comment/index'),

	),

	//URL模式,0:普通模式,1:PATHINFO模式,2:REWRITE模式,3:兼容模式
	'URL_MODEL' => 2,

	//url大小写识别功能
	'URL_CASE_INSENSITIVE' => true,

	//项目所在绝对路径，用于读写文件
	'HDCWS_DIR' => $_SERVER[DOCUMENT_ROOT] . __ROOT__ . '/',

	/****为了项目的移植,上传地址均采用相对地址,即相对于项目根目录hdcws****/

	//logo上传地址
	'UPLOAD_Logo_Dir' => 'uploads/logo',

	//产品上传地址
	'UPLOAD_Product_Dir' => 'uploads/product',
	
	//文章上传地址
	'UPLOAD_Article_Dir' => 'uploads/article',

	//banner上传地址
	'UPLOAD_Banner_Dir' => 'uploads/banner',

	//数据库数据保存地址
	'Save_Db_Dir' => 'uploads/db',

	//系统备份数据库时每个sql分卷大小，值不可太大，否则会导致内存溢出备份、恢复失败，合理大小在512K~10M间，建议5M一卷单位字节 //5M=5*1024*1024=5242880
    'USER_SQL_FILESIZE' => 5242880

);