<?php

/**
 * Dayrui Website Management System
 * 
 * @since			version 2.0.7
 * @author			Dayrui <dayrui@gmail.com>
 * @license     	http://www.dayrui.com/license
 * @copyright		Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

/**
 * 附表结构（由开发者定义）
 */


return array (
  'sql' => 'CREATE TABLE IF NOT EXISTS `{tablename}` (
  `id` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL COMMENT \'作者uid\',
  `catid` smallint(5) unsigned NOT NULL COMMENT \'栏目id\',
  `content` mediumtext COMMENT \'内容\',
  `banner` text,
  `guanlian` text,
  `template` varchar(250) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'附表\'',
  'field' => 
  array (
    0 => 
    array (
      'fieldname' => 'template',
      'fieldtype' => 'Text',
      'relatedid' => '31',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '157',
          'value' => 'show.html',
          'fieldtype' => '',
          'fieldlength' => '',
        ),
        'validate' => 
        array (
          'xss' => '0',
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'isedit' => '0',
          'check' => '',
          'filter' => '',
          'tips' => '也可以在这里指定模板文件',
          'formattr' => '',
        ),
      ),
      'displayorder' => '100',
      'textname' => '模板文件',
    ),
    1 => 
    array (
      'fieldname' => 'guanlian',
      'fieldtype' => 'Related',
      'relatedid' => '31',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'module' => 'news',
          'width' => '80%',
        ),
        'validate' => 
        array (
          'xss' => '0',
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'isedit' => '0',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '4',
      'textname' => '新闻关联',
    ),
    2 => 
    array (
      'fieldname' => 'banner',
      'fieldtype' => 'Files',
      'relatedid' => '31',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '1',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '80%',
          'size' => '2',
          'count' => '10',
          'ext' => 'gif,png,jpg',
          'uploadpath' => '',
        ),
        'validate' => 
        array (
          'xss' => '0',
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'isedit' => '0',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '4',
      'textname' => '封面图',
    ),
    3 => 
    array (
      'fieldname' => 'content',
      'fieldtype' => 'Ueditor',
      'relatedid' => '31',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => 1,
      'ismember' => '1',
      'issearch' => '1',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '90%',
          'height' => '400',
          'mode' => '2',
          'tool' => '\'bold\', \'italic\', \'underline\'',
          'value' => '',
        ),
        'validate' => 
        array (
          'xss' => '1',
          'required' => '1',
          'pattern' => '',
          'errortips' => '',
          'isedit' => '0',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '99',
      'textname' => '简介',
    ),
  ),
);?>