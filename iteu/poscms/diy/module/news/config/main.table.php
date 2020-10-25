<?php

/**
 * Dayrui Website Management System
 * 
 * @since			version 2.3.6
 * @author			Dayrui <dayrui@gmail.com>
 * @license     	http://www.dayrui.com/license
 * @copyright		Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

/**
 * 主表结构（由开发者定义）
 */


return array (
  'sql' => 'CREATE TABLE IF NOT EXISTS `{tablename}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL COMMENT \'栏目id\',
  `title` varchar(255) DEFAULT NULL COMMENT \'主题\',
  `thumb` varchar(255) DEFAULT NULL COMMENT \'缩略图\',
  `keywords` varchar(255) DEFAULT NULL COMMENT \'关键字\',
  `description` text COMMENT \'描述\',
  `hits` mediumint(8) unsigned DEFAULT NULL COMMENT \'浏览数\',
  `uid` mediumint(8) unsigned NOT NULL COMMENT \'作者id\',
  `author` varchar(50) NOT NULL COMMENT \'作者名称\',
  `status` tinyint(2) NOT NULL COMMENT \'审核状态\',
  `url` varchar(255) DEFAULT NULL COMMENT \'地址\',
  `link_id` int(10) NOT NULL DEFAULT \'0\' COMMENT \'同步id\',
  `tableid` smallint(5) unsigned NOT NULL COMMENT \'附表id\',
  `inputip` varchar(15) DEFAULT NULL COMMENT \'录入者ip\',
  `inputtime` int(10) unsigned NOT NULL COMMENT \'录入时间\',
  `updatetime` int(10) unsigned NOT NULL COMMENT \'更新时间\',
   `comments` int(10) unsigned NOT NULL COMMENT \'评论数量\',
   `favorites` int(10) unsigned NOT NULL COMMENT \'收藏数量\',
  `displayorder` tinyint(3) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`,`updatetime`),
  KEY `link_id` (`link_id`),
  KEY `comments` (`comments`),
  KEY `favorites` (`favorites`),
  KEY `status` (`status`),
  KEY `hits` (`hits`),
  KEY `displayorder` (`displayorder`,`updatetime`)
) ENGINE=MyISAM AUTO_INCREMENT=207 DEFAULT CHARSET=utf8 COMMENT=\'主表\'',
  'field' => 
  array (
    0 => 
    array (
      'fieldname' => 'title',
      'fieldtype' => 'Text',
      'relatedid' => '28',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '1',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => 400,
          'fieldtype' => 'VARCHAR',
          'fieldlength' => '255',
        ),
        'validate' => 
        array (
          'xss' => 1,
          'required' => 1,
          'formattr' => 'onblur="check_title();get_keywords(\'keywords\');"',
        ),
      ),
      'displayorder' => '0',
      'textname' => '主题',
    ),
    1 => 
    array (
      'fieldname' => 'thumb',
      'fieldtype' => 'File',
      'relatedid' => '28',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '1',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'ext' => 'jpg,gif,png',
          'size' => 10,
          'width' => 400,
          'fieldtype' => 'VARCHAR',
          'fieldlength' => '255',
        ),
      ),
      'displayorder' => '0',
      'textname' => '缩略图',
    ),
    2 => 
    array (
      'fieldname' => 'keywords',
      'fieldtype' => 'Text',
      'relatedid' => '28',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '1',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => 400,
          'fieldtype' => 'VARCHAR',
          'fieldlength' => '255',
        ),
        'validate' => 
        array (
          'xss' => 1,
            'formattr' => ' data-role="tagsinput"', // tag属性
        ),
      ),
      'displayorder' => '0',
      'textname' => '关键字',
    ),
    3 => 
    array (
      'fieldname' => 'description',
      'fieldtype' => 'Textarea',
      'relatedid' => '28',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '1',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => 500,
          'height' => 60,
          'fieldtype' => 'VARCHAR',
          'fieldlength' => '255',
        ),
        'validate' => 
        array (
          'xss' => 1,
          'filter' => 'dr_clearhtml',
        ),
      ),
      'displayorder' => '0',
      'textname' => '描述',
    ),
  ),
);?>