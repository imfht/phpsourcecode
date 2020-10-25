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

if (empty($_GET['id']))
{
	exit;
}

$confirm_id = htmlspecialchars($_GET['id']);

if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
{
	$confirm_id = '';
}

$sql = 'SELECT code  
	FROM ' . CONFIRM_TABLE . " 
	WHERE session_id = '" . $userdata['session_id'] . "' 
		AND confirm_id = '$confirm_id'";
$result = $db->sql_query($sql);

if ($row = $db->sql_fetchrow($result))
{
	$db->sql_freeresult($result);
	$code = $row['code'];
}
else
{
	exit;
}

require(ROOT_PATH . 'includes/class/kcaptcha.php');

new KCaptcha($code);

?>