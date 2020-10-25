<?php

/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/
$sql = <<<SQL

CREATE TABLE `phpbb_bank` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `holding` int(10) unsigned DEFAULT '0',
  `totalwithdrew` int(10) unsigned DEFAULT '0',
  `totaldeposit` int(10) unsigned DEFAULT '0',
  `opentime` int(10) unsigned NOT NULL,
  `fees` char(5) NOT NULL DEFAULT 'on',
  PRIMARY KEY (`user_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `phpbb_bank_config` (
  `config_name` varchar(255) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  PRIMARY KEY (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bankinterest', '2');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bankfees', '2');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bankpayouttime', '86400');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bankname', '虚拟银行');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bankopened', 'on');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bankholdings', '0');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('banktotaldeposits', '0');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('banktotalwithdrew', '0');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('banklastrestocked', '1402171845');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bank_minwithdraw', '0');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bank_mindeposit', '0');
INSERT INTO `phpbb_bank_config` (`config_name`, `config_value`) VALUES ('bank_interestcut', '0');
SQL;

run_query($sql);

$finish = true;

?>
