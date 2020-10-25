<?php

/**
 * Dayrui Website Management System
 * 
 * @since			version 2.0.3
 * @author			Dayrui <dayrui@gmail.com>
 * @license     	http://www.dayrui.com/license
 * @copyright		Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource		svn://www.dayrui.net/fang/config/data.table.php
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
  `peitao` varchar(255) DEFAULT NULL,
  `images` text,
  `lianxiren` varchar(255) DEFAULT NULL,
  `lianxidianhua` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'附表\'',
  'field' => 
  array (
    0 => 
    array (
      'fieldname' => 'lianxiren',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
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
      'displayorder' => '13',
      'textname' => '联系人',
      'isystem' => 1,
    ),
    1 => 
    array (
      'fieldname' => 'images',
      'fieldtype' => 'Files',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '80%',
          'size' => '5',
          'count' => '5',
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
      'textname' => '房屋图片',
      'isystem' => 1,
    ),
    2 => 
    array (
      'fieldname' => 'zujin2',
      'fieldtype' => 'Group',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'value' => '<label>{zujin}</label><label>元/月，</label><label>{zujinleixing}</label>',
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
      'displayorder' => '11',
      'textname' => '租金',
      'isystem' => 1,
    ),
    3 => 
    array (
      'fieldname' => 'fangwuzhuangkuang',
      'fieldtype' => 'Group',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'value' => '<label>室</label><label>{shi}</label><label>，厅</label><label>{ting}</label><label> ，卫</label><label>{wei}</label><label>，</label><label>{mianji}</label><label>平米，第</label><label>{suozaiceng}</label><label>层，共</label><label>{zongceng}</label><label>层，</label><label>{huxing}</label><label> {zhuangxiu}</label><label> {chaoxiang}</label>',
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
      'displayorder' => '9',
      'textname' => '房屋状况',
      'isystem' => 1,
    ),
    4 => 
    array (
      'fieldname' => 'peitao',
      'fieldtype' => 'Checkbox',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'options' => '拎包入住
家电齐全
可上网
可做饭
可洗澡
空调房
可看电视
有暖气
有车位',
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
      'displayorder' => '10',
      'textname' => '配套',
      'isystem' => 1,
    ),
    5 => 
    array (
      'fieldname' => 'lianxidianhua',
      'fieldtype' => 'Text',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => '0',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '300',
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
      'displayorder' => '14',
      'textname' => '联系电话',
      'isystem' => 1,
    ),
    6 => 
    array (
      'fieldname' => 'content',
      'fieldtype' => 'Ueditor',
      'relatedid' => '19',
      'relatedname' => 'module',
      'isedit' => '1',
      'ismain' => '0',
      'issystem' => '1',
      'ismember' => '1',
      'issearch' => '0',
      'disabled' => '0',
      'setting' => 
      array (
        'option' => 
        array (
          'width' => '100%',
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
      'textname' => '内容',
      'isystem' => 1,
    ),
  ),
);?>