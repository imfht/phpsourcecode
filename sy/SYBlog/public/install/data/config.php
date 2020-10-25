<?php
/**
 * 应用设置
 * 
 * @author ShuangYa
 * @package Blog
 * @category Config
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015-2016 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

return [
	'appName' => 'SYBlog',
	'appNamespace' => 'blog',
	//上传限制
	'notupload' => ['php', 'asp', 'aspx', 'jsp', 'php3', 'php4'],
	//调试模式
	'debug' => TRUE,
	//App根目录
	'dir' => __DIR__,
	//编码
	'charset' => 'utf-8',
	//默认语言
	'language' => 'zh-CN',
	//加密Key
	'cookieKey' => '##cookie##',
	'securityKey' => 'HwlQpTYx',
	'csrf' => FALSE,
	//路由相关配置
	'router' => [
		'type' => 'supervar',
		'module' => 'index',
		'modules' => ['admin', 'index']
	],
	//是否启用URL重写
	'rewrite' => [
		'enable' => TRUE,
		'ext' => 'html', //URL后缀，仅rewrite启用时有效
		'rule' => [
			'index_article_view' => '@root/archives/{{id}}.html',
			'index_article_list' => '@root/category/{{type}}/{{val}}/{{page}}.html'
		],
	],
	'class' => [
	],
	//Cookie相关
	'cookie' => [
		'prefix' => '',
		'expire' => 7200,
		'path' => '@app/',
		'domain' => $_SERVER['HTTP_HOST']
	],
	'mysql' => [
		'host' => '##dbhost##',
		'port' => '##dbport##',
		'user' => '##dbuser##',
		'password' => '##dbpwd##',
		'name' => '##dbname##',
		'prefix' => '##dbprefix##'
	]
];