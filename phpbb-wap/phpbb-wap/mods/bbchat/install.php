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
$sql='CREATE TABLE phpbb_shout (
  shout_id mediumint(8) unsigned NOT NULL auto_increment,
  shout_username varchar(25) NOT NULL,
  shout_user_id mediumint(8) NOT NULL,
  shout_session_time int(11) NOT NULL,
  shout_ip char(8) NOT NULL,
  shout_text text NOT NULL,
  enable_bbcode tinyint(1) NOT NULL,
  enable_html tinyint(1) NOT NULL,
  enable_smilies tinyint(1) NOT NULL,
  shout_bbcode_uid varchar(10) NOT NULL,
  KEY shout_id (shout_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
// SQL 检测

run_query($sql);

$finish = true;


?>