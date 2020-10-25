<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: install.php 2015-5-13 15:24:06Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE `pre_qq_member` (
  `uid` int(10) NOT NULL,
  `openid` varchar(32) NOT NULL,
  `access_token` varchar(32) NOT NULL,
  `conisfeed` tinyint(1) NOT NULL,
  `conispublishfeed` tinyint(1) NOT NULL,
  `conispublisht` tinyint(1) NOT NULL,
  `conisregister` tinyint(1) NOT NULL,
  `conisqzoneavatar` tinyint(1) NOT NULL,
  `conisqqshow` tinyint(1) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM;

CREATE TABLE `pre_qq_member_guest` (
  `nickname` varchar(100) NOT NULL,
  `figureurl_qq` text NOT NULL,
  `gender` varchar(10) NOT NULL,
  `province` varchar(10) NOT NULL,
  `city` varchar(10) NOT NULL,
  `access_token` varchar(32) NOT NULL,
  `openid` varchar(32) NOT NULL,
  PRIMARY KEY (`openid`)
) ENGINE=MyISAM;

CREATE TABLE `pre_qq_member_bindlog` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `openid` varchar(40) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;


EOF;


runquery($sql);

$finish = TRUE;
?>