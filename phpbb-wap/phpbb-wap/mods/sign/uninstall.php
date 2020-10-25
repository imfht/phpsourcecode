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
* 本文件是卸载QQ登录的向导文件
*/

$uninstall_sql = <<<SQL
DROP TABLE `phpbb_sign`;
SQL;

run_query($uninstall_sql);
$finish = true;//卸载成功

?>