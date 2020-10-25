<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

return array(
		//-- 默认数据连接 --
		'DBMASTER' => array(array('127.0.0.1:3306', 'root', 'public', 'savinghousekeeper'),),
		'DBSLAVE' => array(array('127.0.0.1:3306', 'root', 'public', 'savinghousekeeper'),),
		
		//-- 邮箱发件设置 --
		'EMAIL_HOST' => 'smtp.163.com',
		'EMAIL_USERNAME' => 'savinghousekeeper@163.com',
		'EMAIL_PASSWORD' => 'admin888',

		//-- 默认系统设置 --
		'URL_PATH_MOD' => 3,//URL模式(1普通模式、2PATHINFO模式、3兼容模式、4重写模式)
		'URL_HTML_SUFFIX' => '.html',//伪静态前辍
		'PATH_FOLDER' => '/',//网站根目录

		//-- 默认相关设置 --
		'VAR_AJAX_SUBMIT' => 'ajax',// 默认的AJAX提交变量
		'DEFAULT_AJAX_RETURN'   => 'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...
		'TMPL_ACTION_ERROR'     => 'Mod/success', // 默认错误跳转对应的模板文件
		'TMPL_ACTION_SUCCESS'   => 'Mod/success', // 默认成功跳转对应的模板文件
		'VAR_DEFAULT_MOD' => 'mod',//默认模型变量
		'VAR_DEFAULT_ACT' => 'act',//默认控制器变量
		'PAGESIZE' => 10,//无分页数量时默认数量
		'PAGENAME' => 'page',//分页名称
		'RCID' => 'RCSID',//SESSION

		//-- 默认缓存设置 --
		'DATA_CACHE_SWITCH' => true,//是否启用缓存
		'DATA_CACHE_DEFAULT' => 'File',//默认缓存方式
		'DATA_CACHE_PATH' => WWWROOT.'Runtime/Data/',//文件默认缓存目录
		'DATA_CACHE_TIME' => 3600,//数据默认缓存时间
		'DATA_CACHE_SUBDIR' => false,//文件缓存是否使用哈希规则
		'DATA_PATH_LEVEL' => 2,//文件缓存哈希强度
		'DATA_CACHE_CHECK' => true,//是否开启数据校验
		'DATA_CACHE_COMPRESS' => true,//是否压缩缓存
		'MEMCACHE' => array('host'=>'127.0.0.1', 'port'=>'11212'),//Memcache
		'MEMCACHED' => array(array(array('127.0.0.1', '11212', 100),),),//Memcache,支持多个分布式(地址,端口) 公用
		'MEMCACHEQ' => array(array(array('127.0.0.1', '11510', 100),),),//Memcached,支持多个分布式(地址,端口) 公用
		'MEMCACHEDB' => array(array(array('127.0.0.1', '11810', 100),),),//Memcached,支持多个分布式(地址,端口) 公用
		'TOKYO' => array('127.0.0.1', '11511', 100),
		'REDIS' => array('127.0.0.1', '17800', 100),
);
