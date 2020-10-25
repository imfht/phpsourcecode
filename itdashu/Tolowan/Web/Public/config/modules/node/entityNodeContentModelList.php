<?php
$settings = array(
	'article' => array(
		'modelName' => '文章',
		'description' => 'IT大叔为您提供最新的WEB教程、案例分析、实战解析等，这里只有干货！',
		'module' => 'node',
		'access' => 10,
        'keywords' => '网络营销教程',
		'fields' => 'm.node.field_article',
		'comment' => 1,
	),
    'book' => array(
        'modelName' => '书本',
        'description' => 'IT大叔的书本目录，这里只有干货！',
        'module' => 'node',
        'access' => 10,
        'keywords' => '网络营销教程',
        'fields' => 'm.node.field_book',
        'comment' => 1,
    ),
    'file' => array(
        'modelName' => '软件',
        'description' => 'IT大叔为您提供WEB软件-网络营销工具免费下载，这里只有干货！',
        'module' => 'node',
        'access' => 10,
        'keywords' => '网络营销软件,网络营销工具',
        'fields' => 'm.node.field_file',
        'comment' => 1,
    ),
	'page' => array(
		'modelName' => '页面',
		'description' => '一般是页面管理',
		'module' => 'node',
		'access' => 10,
        'keywords' => '',
		'fields' => 'm.node.field_page',
		'comment' => 1,
	),
);
