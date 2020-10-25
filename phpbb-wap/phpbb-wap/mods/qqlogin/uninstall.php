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
ALTER TABLE `phpbb_users` DROP `qq_openid`;
SQL;

run_query($uninstall_sql);
$finish = true;//卸载成功

// 因为run_query()函数在执行SQL过程中出错就会使用message_die()
// 所以下面是不会出现的
//else
//{
	//$finish = false;//卸载失败
//}
?>