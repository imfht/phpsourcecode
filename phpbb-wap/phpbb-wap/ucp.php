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

include(ROOT_PATH . 'common.php');

$userdata = $session->start($user_ip, PAGE_PROFILE);
init_userprefs($userdata);

if (!empty($_POST['sid']) || !empty($_GET['sid']))
{
	$sid = (!empty($_POST['sid'])) ? $_POST['sid'] : $_GET['sid'];
}
else
{
	$sid = '';
}

$page_title = '个人空间';
$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
$script_name = ( $script_name != '' ) ? $script_name . '/ucp.php' : 'ucp.php';
$server_name = trim($board_config['server_name']);
$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';

$server_url = $server_protocol . $server_name . $server_port . $script_name;

function gen_rand_string($hash)
{
	$chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
	
	$max_chars = count($chars) - 1;
	srand( (double) microtime()*1000000);
	
	$rand_str = '';
	for($i = 0; $i < 8; $i++)
	{
		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
	}

	return ( $hash ) ? md5($rand_str) : $rand_str;
}

if ( isset($_GET['mode']) || isset($_POST['mode']) )
{
	$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : $_POST['mode'];
	$mode = htmlspecialchars($mode);

	if ( $mode == 'viewprofile' )
	{
		include(ROOT_PATH . 'includes/ucp/viewprofile.php');
		exit;
	}
	else if ( $mode == 'viewfiles' )
	{
		include(ROOT_PATH . 'includes/ucp/viewfiles.php');
		exit;
	}
	else if ( $mode == 'editprofile' || $mode == 'register' )
	{
		if ( !$userdata['session_logged_in'] && $mode == 'editprofile' )
		{
			login_back('ucp.php?mode=editprofile');
		}

		include(ROOT_PATH . 'includes/ucp/register.php');
		exit;
	}
	else if ( $mode == 'selectstyle' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			login_back('ucp.php?mode=selectstyle');
		}

		include(ROOT_PATH . 'includes/ucp/selectstyle.php');
		exit;
	}
	else if ( $mode == 'money' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			login_back('ucp.php?mode=money');
		}

		include(ROOT_PATH . 'includes/ucp/money.php');
		exit;
	}
	else if ( $mode == 'editconfig' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			login_back('ucp.php?mode=editconfig');
		}

		include(ROOT_PATH . 'includes/ucp/editconfig.php');
		exit;
	}
	else if ( $mode == 'delete' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid('login.php', true));
		}

		include(ROOT_PATH . 'includes/ucp/delete.php');
		exit;
	}
	else if ( $mode == 'editprofileinfo' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			login_back('ucp.php?mode=editprofileinfo');
		}

		include(ROOT_PATH . 'includes/ucp/editprofileinfo.php');
		exit;
	}
	else if ( $mode == 'confirm' )
	{
		include(ROOT_PATH . 'includes/ucp/confirm.php');
		exit;
	}
	else if ( $mode == 'sendpassword' )
	{
		include(ROOT_PATH . 'includes/ucp/sendpasswd.php');
		exit;
	}
	else if ( $mode == 'activate' )
	{
		include(ROOT_PATH . 'includes/ucp/activate.php');
		exit;
	}
	else if ( $mode == 'email' )
	{
		include(ROOT_PATH . 'includes/ucp/email.php');
		exit;
	}
	else if ( $mode == 'clone' )
	{
		include(ROOT_PATH . 'includes/ucp/clone.php');
		exit;
	}
	elseif ($mode == 'lock')
	{
		include(ROOT_PATH . 'includes/ucp/lock.php');
		exit;
	}
	elseif ($mode == 'ban')
	{
		include(ROOT_PATH . 'includes/ucp/banuser.php');
		exit;
	}
	elseif ($mode == 'main')
	{
		include(ROOT_PATH . 'includes/ucp/main.php');
		exit;
	}
	elseif ($mode == 'manage')
	{
		include(ROOT_PATH . 'includes/ucp/manage.php');
		exit;
	}
	elseif ($mode == 'friends')
	{
		include(ROOT_PATH . 'includes/ucp/friends.php');
		exit;
	}
	elseif ($mode == 'guestbook')
	{
		include(ROOT_PATH . 'includes/ucp/guestbook.php');
		exit;
	}
	elseif ($mode == 'topic_collect')
	{
		include(ROOT_PATH . 'includes/ucp/topic_collect.php');
		exit;
	}
}

redirect(append_sid('index.php', true));
?>