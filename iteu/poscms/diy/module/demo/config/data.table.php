<?php

/**
 * v3.1.0
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
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'附表\'',
  'field' => 
  array (
    0 => 
    array (
      'fieldname' => 'content',
      'fieldtype' => 'Ueditor',
      'relatedid' => '17',
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
          'mode' => 1,
          'width' => '90%',
          'height' => 400,
        ),
        'validate' => 
        array (
          'xss' => 1,
          'required' => 1,
        ),
      ),
      'displayorder' => '0',
      'textname' => '内容',
    ),
  ),
);?>