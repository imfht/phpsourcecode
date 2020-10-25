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
	  `content` mediumtext DEFAULT NULL COMMENT "内容",
	  `images` text DEFAULT NULL COMMENT "图片",
	  UNIQUE KEY `id` (`id`),
	  KEY `uid` (`uid`),
	  KEY `catid` (`catid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="附表";
	',
	
	'field' => array(
		array(
			'textname' => '图片',	// 字段显示名称
			'fieldname' => 'images',	// 字段名称
			'fieldtype'	=> 'Files',	// 字段类别
			'setting' => array(
				'option' => array(
					'ext' => 'gif,png,jpg', // 扩展名限制
					'size' => 10, // 文件大小
					'count' => 10, // 文件数量
				)
			)
		),
		array(
			'textname' => '详情', // 字段显示名称
			'fieldname' => 'content', // 字段名称
			'fieldtype'	=> 'Ueditor', // 字段类别
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