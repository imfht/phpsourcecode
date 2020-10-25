<?php
/**
* @package phpBB-WAP MODS
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

// 安装执行的语句
$sql = <<<SQL
CREATE TABLE phpbb_sign (
  sign_id mediumint(8) unsigned NOT NULL auto_increment,
  sign_user_id mediumint(8) NOT NULL default '-1',
  sign_time int(11) NOT NULL default '0',
  sign_talk text,
  PRIMARY KEY  (sign_id),
  KEY sign_user_id (sign_user_id),
  KEY sign_time (sign_time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;

run_query($sql);//执行SQL

$finish = true;//安装成功

?>