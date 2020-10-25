<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 附表结构（由开发者定义）
 *
 * sql: 初始化SQL语句，用{tablename}表示表名称
 * filed：初始化的自定义字段，可以用来由用户修改的字段
 */

return array(

	'sql' => '
	CREATE TABLE IF NOT EXISTS `{tablename}` (
	  `id` int(10) unsigned NOT NULL,
	  `uid` mediumint(8) unsigned NOT NULL COMMENT "作者uid",
	  `catid` smallint(5) unsigned NOT NULL COMMENT "栏目id",
	  `file` varchar(255) DEFAULT NULL COMMENT "文件",
	  `demo` varchar(255) DEFAULT NULL COMMENT "演示地址",
	  `siteurl` varchar(255) DEFAULT NULL COMMENT "官方网站",
	  `images` text DEFAULT NULL COMMENT "图片展示",
	  `content` mediumtext DEFAULT NULL COMMENT "内容",
	  UNIQUE KEY `id` (`id`),
	  KEY `uid` (`uid`),
	  KEY `catid` (`catid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="附表";
	',
	
	'field' => array(
		array(
			'textname' => '演示地址', // 字段显示名称
			'fieldname' => 'demo', // 字段名称
			'fieldtype'	=> 'Text', // 字段类别
			'displayorder' => 11, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 300, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'filter' => 'prep_url', // URL补全
				),
			)
		),
		array(
			'textname' => '官方网站', // 字段显示名称
			'fieldname' => 'siteurl', // 字段名称
			'fieldtype'	=> 'Text', // 字段类别
			'displayorder' => 10, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 300, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'filter' => 'prep_url', // URL补全
				),
			)
		),
		array(
			'textname' => '上传文件',	// 字段显示名称
			'fieldname' => 'file',	// 字段名称
			'fieldtype'	=> 'File',	// 字段类别
			'displayorder' => 12, // 排序号 
			'displayorder' => 3, // 排序号 
			'setting' => array(
				'option' => array(
					'ext' => 'zip,rar,7z,tar,gz', // 扩展名限制
					'size' => 10, // 文件大小
					'count' => 1, // 文件数量
				)
			)
		),
		array(
			'textname' => '更多图片',	// 字段显示名称
			'fieldname' => 'images',	// 字段名称
			'fieldtype'	=> 'Files',	// 字段类别
			'displayorder' => 98, // 排序号 
			'setting' => array(
				'option' => array(
					'ext' => 'gif,png,jpg', // 扩展名限制
					'size' => 10, // 文件大小
					'count' => 10, // 文件数量
				)
			)
		),
		array(
			'textname' => '软件简介', // 字段显示名称
			'fieldname' => 'content', // 字段名称
			'fieldtype'	=> 'Ueditor', // 字段类别
			'displayorder' => 99, // 排序号 
			'setting' => array(
				'option' => array(
					'mode' => 2, // 工具栏模式
					'width' => '90%', // 表单宽度
					'height' => 400, // 表单高度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'required' => 1, // 表示必填
				)
			)
		)
	)

);