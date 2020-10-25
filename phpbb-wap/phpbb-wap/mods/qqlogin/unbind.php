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

if (($userdata['qq_openid'] == '') || ($userdata['user_id'] == ANONYMOUS))
{
	redirect(append_sid('index.php', true));
}

if ( isset($_POST['cancel']) )
{
	redirect(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $user, true));
}

$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

if( !$confirm )
{
	page_header('解除QQ登录');
	
	$template->set_filenames(array(
		'confirm' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' 	=> '解除QQ登录',
		'MESSAGE_TEXT'		=> '解除QQ登录后您将无法使用QQ登录本站并且会退出当前登录，请问是否解除QQ登录？',
		'L_YES' 			=> '是',
		'L_NO' 				=> '否',
		'S_CONFIRM_ACTION' 	=> append_sid(ROOT_PATH . 'loading.php?mod=qqlogin&load=unbind'))
	);

	$template->pparse('confirm');

	page_footer();
}

$sql = 'UPDATE ' . USERS_TABLE . " 
	SET qq_openid = '' 
	WHERE user_id = " . (int) $userdata['user_id'];

if(!$db->sql_query($sql))
{
	trigger_error('无法更新用户open id', E_USER_WARNING);
}

if( $userdata['session_logged_in'] )
{
	$session->destroy();
}

trigger_error('QQ登录功能已经解除！<br /><a href="' . append_sid('index.php') . '">&lt;--快速退出</a>', E_USER_ERROR);

?>