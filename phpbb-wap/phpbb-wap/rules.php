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

define('IN_PHPBB', true);
define('ROOT_PATH', './');
require(ROOT_PATH . 'common.php');

$forum_id = get_var('f', 0);
$privmsg = (!$forum_id) ? true : false;

$userdata = $session->start($user_ip, PAGE_RULES);
init_userprefs($userdata);

if ( isset($_GET['mode']) ){
	$mode = htmlspecialchars($_GET['mode']);
}else{
	$mode = '';
}

if ( isset($_GET['act']) )
{
	$action = htmlspecialchars($_GET['act']);
}
else
{
	$action = '';
}

$rule_cat_id = ( isset($_GET['crid']) ) ? abs(intval($_GET['crid'])) : '';

$is_rules_auth = ( $userdata['user_level'] == ADMIN ) ? true : false;

switch( $mode )
{
	case 'addcat':
	case 'editcat':
		require ROOT_PATH . 'includes/rules/editcat.php';
	break;
		
	case 'addrule':
	case 'editrule':
		require ROOT_PATH . 'includes/rules/editrule.php';
	break;
		
	case 'delcat':
	case 'delrule':
		require ROOT_PATH . 'includes/rules/delrule.php';
	break;
		
	case 'faq':
		require ROOT_PATH . 'includes/rules/faq.php';
	break;

	default:
		require ROOT_PATH . 'includes/rules/default.php';
}

?>