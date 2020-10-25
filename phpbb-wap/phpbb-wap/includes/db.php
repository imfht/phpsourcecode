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

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

require(ROOT_PATH . 'includes/class/mysql.php');

$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);

if(!$db->db_connect_id)
{
	trigger_error('无法链接到数据库！', E_USER_ERROR);
}

$db->sql_query('SET NAMES utf8');

?>