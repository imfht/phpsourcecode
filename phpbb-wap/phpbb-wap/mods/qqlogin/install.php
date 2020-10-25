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

/*
* 本文件是安装QQ登录的向导文件
*/

// 安装执行的语句
$sql = <<<SQL
ALTER TABLE `phpbb_users` ADD `qq_openid` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
SQL;

run_query($sql);//执行SQL

$finish = true;//安装成功

?>