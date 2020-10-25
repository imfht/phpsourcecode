<?php
/**
* @package phpBB-WAP
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

define('IN_PHPBB', true);
define('ROOT_PATH', './');
require(ROOT_PATH . 'common.php');

$userdata = $session->start($user_ip, PAGE_MODS);
init_userprefs($userdata);

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode)
{
	case 'cat':
		require ROOT_PATH . 'includes/links/cat.php';
		break;
	case 'view':
		require ROOT_PATH . 'includes/links/view.php';
		break;
	case 'edit':
		require ROOT_PATH . 'includes/links/edit.php';
		break;
	case 'manage':
		require ROOT_PATH . 'includes/links/manage.php';
		break;
	case 'join':
		require ROOT_PATH . 'includes/links/join.php';
		break;
	case 'out':
		require ROOT_PATH . 'includes/links/out.php';
		break;
	break;
	case 'in':
		require ROOT_PATH . 'includes/links/in.php';
		break;
	default:
		require ROOT_PATH . 'includes/links/link.php';
		break;
}
?>