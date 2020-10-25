<?php

/**
 * Dayrui Website Management System
 *
 * @since			version 2.0.3
 * @author			Dayrui <dayrui@gmail.com>
 * @license     	http://www.dayrui.com/license
 * @copyright		Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource		svn://www.dayrui.net/fang/config/main.table.php
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
  `status` tinyint(2) NOT NULL COMMENT "审核状态",
  `url` varchar(255) DEFAULT NULL COMMENT \'地址\',
  `link_id` int(10) NOT NULL DEFAULT 0 COMMENT "同步id",
  `tableid` smallint(5) unsigned NOT NULL COMMENT "附表id",
  `inputip` varchar(15) DEFAULT NULL COMMENT \'录入者ip\',
  `inputtime` int(10) unsigned NOT NULL COMMENT \'录入时间\',
  `updatetime` int(10) unsigned NOT NULL COMMENT \'更新时间\',
  `comments` int(10) unsigned NOT NULL COMMENT \'评论数量\',
  `favorites` int(10) unsigned NOT NULL COMMENT \'收藏数量\',
  `displayorder` tinyint(3) NOT NULL DEFAULT \'0\',
  `area` mediumint(8) unsigned DEFAULT NULL,
  `xiaoqumingcheng` varchar(255) DEFAULT NULL,
  `dizhi` varchar(255) DEFAULT NULL,
  `weizhi_lng` decimal(9,6) DEFAULT NULL,
  `weizhi_lat` decimal(9,6) DEFAULT NULL,
  `huxing` varchar(255) DEFAULT NULL,
  `shi` int(10) DEFAULT NULL,
  `ting` int(10) DEFAULT NULL,
  `wei` int(10) DEFAULT NULL,
  `mianji` int(10) DEFAULT NULL,
  `zhuangxiu` varchar(255) DEFAULT NULL,
  `chaoxiang` varchar(255) DEFAULT NULL,
  `zongceng` varchar(255) DEFAULT NULL,
  `suozaiceng` varchar(255) DEFAULT NULL,
  `zujin` int(10) DEFAULT NULL,
  `zujinleixing` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`,`updatetime`),
  KEY `link_id` (`link_id`),
  KEY `status` (`status`),
  KEY `hits` (`hits`),
  KEY `comments` (`comments`),
  KEY `favorites` (`favorites`),
  KEY `displayorder` (`displayorder`,`updatetime`),
  KEY `zujin` (`zujin`),
  KEY `area` (`area`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT=\'主表\'',
  'field' =>
  array (
    0 =>
    array (
      'fieldname' => 'zujinleixing',
      'fieldtype' => 'Select',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'options' => '押一付三
押一付二
押一付一
半年付
年付
面议',
          'value' => '',
          'fieldtype' => '',
          'fieldlength' => '',
        ),
        'validate' =>
        array (
          'xss' => '0',
          'required' => '0',
          'pattern' => '',
          'errortips' => 'a:1:{s:5:\\',
          'isedit' => '0',
          'check' => '',
          'filter' => '',
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '租金类型',
      'isystem' => 1,
    ),
    1 =>
    array (
      'fieldname' => 'zujin',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '200',
          'value' => '',
          'fieldtype' => 'INT',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '租金',
      'isystem' => 1,
    ),
    2 =>
    array (
      'fieldname' => 'suozaiceng',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '50',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '所在层',
      'isystem' => 1,
    ),
    3 =>
    array (
      'fieldname' => 'zongceng',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '50',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '总层',
      'isystem' => 1,
    ),
    4 =>
    array (
      'fieldname' => 'chaoxiang',
      'fieldtype' => 'Select',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'options' => '南北通透
东西向
朝南
朝北
朝东
朝西',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '朝向',
      'isystem' => 1,
    ),
    5 =>
    array (
      'fieldname' => 'zhuangxiu',
      'fieldtype' => 'Select',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'options' => '毛坯
简装修
中等装修
精装修
豪华装修',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '装修',
      'isystem' => 1,
    ),
    6 =>
    array (
      'fieldname' => 'mianji',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '50',
          'value' => '',
          'fieldtype' => 'INT',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '面积',
      'isystem' => 1,
    ),
    7 =>
    array (
      'fieldname' => 'wei',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '50',
          'value' => '',
          'fieldtype' => 'INT',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '卫',
      'isystem' => 1,
    ),
    8 =>
    array (
      'fieldname' => 'ting',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '50',
          'value' => '',
          'fieldtype' => 'INT',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '厅',
      'isystem' => 1,
    ),
    9 =>
    array (
      'fieldname' => 'shi',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '50',
          'value' => '',
          'fieldtype' => 'INT',
          'fieldlength' => '20',
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
      'displayorder' => '0',
      'textname' => '室',
      'isystem' => 1,
    ),
    10 =>
    array (
      'fieldname' => 'huxing',
      'fieldtype' => 'Select',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'options' => '一居
二居
三居
四居
四居以上',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '0',
      'textname' => '户型',
      'isystem' => 1,
    ),
    11 =>
    array (
      'fieldname' => 'weizhi',
      'fieldtype' => 'Baidumap',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '700',
          'height' => '430',
          'level' => '15',
          'city' => '成都',
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
      'displayorder' => '8',
      'textname' => '位置',
      'isystem' => 1,
    ),
    12 =>
    array (
      'fieldname' => 'xiaoqumingcheng',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '200',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '6',
      'textname' => '小区名称',
      'isystem' => 1,
    ),
    13 =>
    array (
      'fieldname' => 'dizhi',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '500',
          'value' => '',
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
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '7',
      'textname' => '地址',
      'isystem' => 1,
    ),
    14 =>
    array (
      'fieldname' => 'area',
      'fieldtype' => 'Linkage',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '0',
      'ismember' => '1',
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
          'xss' => '0',
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
      'displayorder' => '5',
      'textname' => '区域',
      'isystem' => 1,
    ),
    15 =>
    array (
      'fieldname' => 'title',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '1',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '400',
          'value' => '',
          'fieldtype' => 'VARCHAR',
          'fieldlength' => '255',
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
          'formattr' => 'onblur="check_title();get_keywords(\'keywords\');"',
        ),
      ),
      'displayorder' => '1',
      'textname' => '主题',
      'isystem' => 1,
    ),
    16 =>
    array (
      'fieldname' => 'thumb',
      'fieldtype' => 'File',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '1',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'size' => '10',
          'ext' => 'jpg,gif,png',
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
      'displayorder' => '3',
      'textname' => '缩略图',
      'isystem' => 1,
    ),
    17 =>
    array (
      'fieldname' => 'keywords',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '1',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '400',
          'value' => '',
          'fieldtype' => 'VARCHAR',
          'fieldlength' => '255',
        ),
        'validate' =>
        array (
          'xss' => '1',
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'isedit' => '0',
          'check' => '',
          'filter' => '',
            'formattr' => ' data-role="tagsinput"', // tag属性
        ),
      ),
      'displayorder' => '2',
      'textname' => '关键字',
      'isystem' => 1,
    ),
    18 =>
    array (
      'fieldname' => 'description',
      'fieldtype' => 'Textarea',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '1',
      'issystem' => '1',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '1',
      'setting' =>
      array (
        'option' =>
        array (
          'width' => '500',
          'height' => '60',
          'value' => '',
        ),
        'validate' =>
        array (
          'xss' => '1',
          'required' => '0',
          'pattern' => '',
          'errortips' => '',
          'isedit' => '0',
          'check' => '',
          'filter' => 'dr_clearhtml',
          'tips' => '',
          'formattr' => '',
        ),
      ),
      'displayorder' => '99',
      'textname' => '描述',
      'isystem' => 1,
    ),
  ),
);?>