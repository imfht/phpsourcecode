<?php

/**
 * v3.1.0
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
  `status` tinyint(2) NOT NULL COMMENT \'状态\',
  `url` varchar(255) DEFAULT NULL COMMENT \'地址\',
  `link_id` int(10) NOT NULL DEFAULT \'0\' COMMENT \'同步id\',
  `tableid` smallint(5) unsigned NOT NULL COMMENT \'附表id\',
  `inputip` varchar(15) DEFAULT NULL COMMENT \'录入者ip\',
  `inputtime` int(10) unsigned NOT NULL COMMENT \'录入时间\',
  `updatetime` int(10) unsigned NOT NULL COMMENT \'更新时间\',
  `comments` int(10) unsigned NOT NULL COMMENT \'评论数量\',
  `favorites` int(10) unsigned NOT NULL COMMENT \'收藏数量\',
  `displayorder` tinyint(3) NOT NULL DEFAULT \'0\',
  `fuxuankuang` varchar(255) DEFAULT NULL,
  `ysxqfyb` varchar(10) DEFAULT NULL,
  `yuedushoufei` text,
  `dangewenjian` varchar(255) DEFAULT NULL,
  `dgwjhtp` text,
  `tpzy` varchar(255) DEFAULT NULL,
  `ldcd` mediumint(8) unsigned DEFAULT NULL,
  `spsx` text,
  `nrglxw` text,
  `spwj` text,
  `danxuananniu` varchar(255) DEFAULT NULL,
  `xialaxuanze` varchar(255) DEFAULT NULL,
  `baiduditu_lng` decimal(9,6) DEFAULT NULL,
  `baiduditu_lat` decimal(9,6) DEFAULT NULL,
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
      'fieldname' => 'fuxuankuang',
      'fieldtype' => 'Checkbox',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'options' => '选项名称1|1
选项名称2|2
选项名称3|3',
          'value' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '复选框',
    ),
    1 => 
    array (
      'fieldname' => 'ysxqfyb',
      'fieldtype' => 'Color',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'field' => 'title',
          'value' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '1',
      ),
      'displayorder' => '0',
      'textname' => '颜色选取（放右边）',
    ),
    2 => 
    array (
      'fieldname' => 'yuedushoufei',
      'fieldtype' => 'Fees',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '80%',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '阅读收费',
    ),
    3 => 
    array (
      'fieldname' => 'dangewenjian',
      'fieldtype' => 'File',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'size' => '12',
          'ext' => 'zip',
          'uploadpath' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '单个文件',
    ),
    4 => 
    array (
      'fieldname' => 'dgwjhtp',
      'fieldtype' => 'Files',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '90%',
          'size' => '10',
          'count' => '10',
          'ext' => 'jpg,gif,png,exe,rar,zip',
          'uploadpath' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '多个文件或图片',
    ),
    5 => 
    array (
      'fieldname' => 'tpzy',
      'fieldtype' => 'Image',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '80%',
          'size' => '10',
          'count' => '5',
          'autodown' => '0',
          'uploadpath' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '图片专用',
    ),
    6 => 
    array (
      'fieldname' => 'ldcd',
      'fieldtype' => 'Linkage',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'linkage' => 'address',
          'value' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '联动菜单',
    ),
    7 => 
    array (
      'fieldname' => 'spsx',
      'fieldtype' => 'Property',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '100%',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '商品属性',
    ),
    8 => 
    array (
      'fieldname' => 'nrglxw',
      'fieldtype' => 'Related',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'module' => 'news',
          'width' => '90%',
          'limit' => '22',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '内容关联（新闻）',
    ),
    9 => 
    array (
      'fieldname' => 'spwj',
      'fieldtype' => 'Video',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '90%',
          'size' => '10',
          'ext' => 'mp4',
          'uploadpath' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '视频文件',
    ),
    10 => 
    array (
      'fieldname' => 'danxuananniu',
      'fieldtype' => 'Radio',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'options' => '选项名称1|1
选项名称2|2
选项名称3|3',
          'value' => '',
          'fieldtype' => '',
          'fieldlength' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '单选按钮',
    ),
    11 => 
    array (
      'fieldname' => 'xialaxuanze',
      'fieldtype' => 'Select',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'options' => '选项名称1|1
选项名称2|2
选项名称3|3',
          'value' => '',
          'fieldtype' => '',
          'fieldlength' => '',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '下拉选择',
    ),
    12 => 
    array (
      'fieldname' => 'baiduditu',
      'fieldtype' => 'Baidumap',
      'relatedid' => '17',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => 1,
      'ismember' => '0',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '700',
          'height' => '430',
          'level' => '15',
          'city' => '北京',
        ),
        'validate' => 
        array (
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
        'is_right' => '0',
      ),
      'displayorder' => '0',
      'textname' => '百度地图',
    ),
    13 => 
    array (
      'fieldname' => 'title',
      'fieldtype' => 'Text',
      'relatedid' => '17',
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
    14 => 
    array (
      'fieldname' => 'thumb',
      'fieldtype' => 'File',
      'relatedid' => '17',
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
    15 => 
    array (
      'fieldname' => 'keywords',
      'fieldtype' => 'Text',
      'relatedid' => '17',
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
          'formattr' => ' data-role="tagsinput"',
        ),
      ),
      'displayorder' => '0',
      'textname' => '关键字',
    ),
    16 => 
    array (
      'fieldname' => 'description',
      'fieldtype' => 'Textarea',
      'relatedid' => '17',
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