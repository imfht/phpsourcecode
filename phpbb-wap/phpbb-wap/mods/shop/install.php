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

CREATE TABLE `phpbb_shop_config` (
  `config_name` varchar(25) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  PRIMARY KEY (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES ('top_ad', '100');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('foot_ad', '100');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('max_day', '7');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('min_day', '1');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('max_top_ad', '4');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('max_foot_ad', '3');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('buy_username', '100');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('buy_namecolor', '1000');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('buy_rank', '100');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('time_click', '86400');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('good', '0');
INSERT INTO `phpbb_shop_config` (`config_name`, `config_value`) VALUES('ad', '0');

CREATE TABLE `phpbb_shop_ad` (
  `ad_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(255) NOT NULL,
  `ad_type` tinyint(1) NOT NULL,
  `ad_time` int(11) NOT NULL,
  `ad_url` varchar(255) NOT NULL,
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `phpbb_shop_good` (
  `good_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `good_name` varchar(255) NOT NULL,
  `good_url` varchar(255) NOT NULL,
  `good_points` int(11) NOT NULL,
  PRIMARY KEY (`good_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `phpbb_shop_qq` (
  `qq` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  UNIQUE KEY `qq` (`qq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;

run_query($sql);

$finish = true;

?>