<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 主表结构（由开发者定义）
 *
 * sql: 初始化SQL语句，用{tablename}表示表名称
 * filed：初始化的自定义字段，可以用来由用户修改的字段
 */

return array(

	'sql' => '
	CREATE TABLE IF NOT EXISTS `{tablename}` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `catid` smallint(5) unsigned NOT NULL COMMENT "栏目id",
	  `title` varchar(255) DEFAULT NULL COMMENT "主题",
	  `thumb` varchar(255) DEFAULT NULL COMMENT "缩略图",
	  `version` varchar(255) DEFAULT NULL COMMENT "版本号",
	  `language` varchar(255) DEFAULT NULL COMMENT "软件语言",
	  `license` varchar(255) DEFAULT NULL COMMENT "软件授权",
	  `os` varchar(255) DEFAULT NULL COMMENT "运行环境",
	  `keywords` varchar(255) DEFAULT NULL COMMENT "关键字",
	  `description` text DEFAULT NULL COMMENT "描述",
	  `hits` mediumint(8) unsigned DEFAULT NULL COMMENT "浏览数",
	  `uid` mediumint(8) unsigned NOT NULL COMMENT "作者id",
	  `author` varchar(50) NOT NULL COMMENT "作者名称",
	  `status` tinyint(2) NOT NULL COMMENT "审核状态",
	  `url` varchar(255) DEFAULT NULL COMMENT "地址",
      `link_id` int(10) NOT NULL DEFAULT 0 COMMENT "同步id",
      `tableid` smallint(5) unsigned NOT NULL COMMENT "附表id",
	  `inputip` varchar(15) DEFAULT NULL COMMENT "录入者ip",
	  `inputtime` int(10) unsigned NOT NULL COMMENT "录入时间",
	  `updatetime` int(10) unsigned NOT NULL COMMENT "更新时间",
  	  `comments` int(10) unsigned  DEFAULT 0 COMMENT "评论数量",
  	  `favorites` int(10) unsigned  DEFAULT 0 COMMENT "收藏数量",
	  `displayorder` tinyint(3) NOT NULL DEFAULT "0",
	  PRIMARY KEY (`id`),
	  KEY `uid` (`uid`),
	  KEY `catid` (`catid`,`updatetime`),
	  KEY `status` (`status`),
	  KEY `link_id` (`link_id`),
	  KEY `comments` (`comments`),
	  KEY `favorites` (`favorites`),
	  KEY `hits` (`hits`),
	  KEY `displayorder` (`displayorder`,`updatetime`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="主表";
	',
	
	'field' => array(
		array(
			'textname' => '软件名称',	// 字段显示名称
			'fieldname' => 'title',	// 字段名称
			'fieldtype'	=> 'Text',	// 字段类别
			'displayorder' => 1, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 200, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'formattr' => 'onblur="check_title();get_keywords(\'keywords\');"', // 表单附件参数
					'required' => 1, // 表示必填
				)
			)
		),
		array(
			'textname' => '缩略图', // 字段显示名称
			'fieldname' => 'thumb',	// 字段名称
			'fieldtype'	=> 'File', // 字段类别
			'displayorder' => 97, // 排序号 
			'setting' => array(
				'option' => array(
					'ext' => 'jpg,gif,png',
					'size' => 10, 
					'width' => 400, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255', // 字段长度
				)
			)
		),
		array(
			'textname' => '关键字', // 字段显示名称
			'fieldname' => 'keywords', // 字段名称
			'fieldtype'	=> 'Text', // 字段类别
			'displayorder' => 4, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 400, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'formattr' => ' data-role="tagsinput"', // tag属性
				),
			)
		),
		array(
			'textname' => '描述', // 字段显示名称
			'fieldname' => 'description', // 字段名称
			'fieldtype'	=> 'Textarea', // 字段类别
			'displayorder' => 5, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 500, // 表单宽度
					'height' => 60,
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'filter' => 'dr_clearhtml', // 过滤html 
				),
			)
		),
		array(
			'textname' => '软件版本', // 字段显示名称
			'fieldname' => 'version', // 字段名称
			'fieldtype'	=> 'Text', // 字段类别
			'displayorder' => 2, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 200, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
				),
			)
		),
		array(
			'textname' => '软件语言', // 字段显示名称
			'fieldname' => 'language', // 字段名称
			'fieldtype'	=> 'Select', // 字段类别
			'displayorder' => 7, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 200, // 表单宽度
					'options' => '简体中文'.PHP_EOL.'繁体中文'.PHP_EOL.'英文'.PHP_EOL.'多国语言',
				),
				'validate' => array(
					'xss' => 1, // xss过滤
				),
			)
		),
		array(
			'textname' => '软件授权', // 字段显示名称
			'fieldname' => 'license', // 字段名称
			'fieldtype'	=> 'Select', // 字段类别
			'displayorder' => 8, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 200, // 表单宽度
					'options' => '共享版'.PHP_EOL.'免费版'.PHP_EOL.'商业版'.PHP_EOL.'试用版'.PHP_EOL.'开源软件',
				),
				'validate' => array(
					'xss' => 1, // xss过滤
				),
			)
		),
		array(
			'textname' => '适用环境', // 字段显示名称
			'fieldname' => 'os', // 字段名称
			'fieldtype'	=> 'Checkbox', // 字段类别
			'displayorder' => 9, // 排序号 
			'setting' => array(
				'option' => array(
					'width' => 200, // 表单宽度
					'options' => 'WinXp'.PHP_EOL.'Win7'.PHP_EOL.'Win8'.PHP_EOL.'Linux'.PHP_EOL.'Mac',
				),
				'validate' => array(
					'xss' => 1, // xss过滤
				),
			)
		),
	)

);