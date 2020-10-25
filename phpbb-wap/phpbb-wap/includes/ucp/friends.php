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

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('您选择的是游客或用户不存在', E_USER_ERROR);
}

if (!$profiledata = get_userdata($_GET[POST_USERS_URL]))
{
	trigger_error('无法取得用户数据！', E_USER_ERROR);
}

if ( $userdata['user_id'] != $profiledata['user_id'] )
{
	if ($userdata['user_level'] != ADMIN )
	{
		trigger_error('您没有权限查看该用户的好友信息!<br />点击 <a href="' . append_sid(ROOT_PATH . 'ucp.php?mode=manage&' . POST_USERS_URL . '=' . $userdata['user_id']) . '">这里</a> 进入我的地盘管理', E_USER_ERROR);
	}
}

$action = get_var('action', '');
$f = get_var('f', '');

if ($action == 'add')
{
	$sql = 'SELECT user_id, username
		FROM ' . USERS_TABLE . '
		WHERE user_id = ' . (int)$f;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询该用户的信息', E_USER_WARNING);
	}

	if (!$db->sql_numrows($result))
	{
		trigger_error('您要添加的用户不存在', E_USER_ERROR);
	}

	$row = $db->sql_fetchrow($result);

	$sql = 'SELECT friend_id
		FROM ' . FRIENDS_TABLE . '
		WHERE user_id = ' . $row['user_id'] . '
			AND ucp_id = ' . $profiledata['user_id'];

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询好友信息', E_USER_WARNING);
	}

	if ($db->sql_numrows($result))
	{
		// 判断用户是否接受
		trigger_error('您已添加该用户，如果他没有接受您的添加请求请耐心等待' . back_link(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $row['user_id'])), E_USER_ERROR);
	}

	$sql = "INSERT INTO " . FRIENDS_TABLE . " (user_id, remark, ucp_id)
		VALUES (" . $row['user_id'] . ", '" . $row['username'] . "', " . $profiledata['user_id'] . ")";

	if (!$db->sql_query($sql))
	{
		trigger_error('无法添加好友', E_USER_WARNING);
	}

	trigger_error('您的申请已提交，请耐心等待审核' . back_link(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $row['user_id'])), E_USER_ERROR);
}
elseif ($action == 'del')
{
	$sql = 'SELECT friend_id
		FROM ' . FRIENDS_TABLE . '
		WHERE friend_id = ' . (int) $f;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询好友信息', E_USER_WARNING);
	}

	if (!$db->sql_numrows($result))
	{
		trigger_error('他不是你的好友', E_USER_ERROR);
	}

	if ( isset($_POST['cancel']) )
	{
		redirect(append_sid('ucp.php?mode=friends&' . POST_USERS_URL . '=' . $profiledata['user_id']), true);
	}

	$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

	if( !$confirm )
	{
		page_header('删除好友');
		
		$template->set_filenames(array(
			'confirm' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' 	=> '删除好友',
			'MESSAGE_TEXT'		=> '真的要把你的好友删除？',
			'L_YES' 			=> '是',
			'L_NO' 				=> '否',
			'S_CONFIRM_ACTION' 	=> append_sid('ucp.php?mode=friends&action=del&' . POST_USERS_URL . '=' . $profiledata['user_id'] . '&f=' . $f))
		);

		$template->pparse('confirm');

		page_footer();
	}

	$sql = 'DELETE FROM ' . FRIENDS_TABLE . '
		WHERE friend_id = ' . (int) $f;

	if (!$db->sql_query($sql))
	{
		trigger_error('无法删除好友', E_USER_WARNING);
	}

	trigger_error('删除成功' . back_link(append_sid('ucp.php?mode=friends&' . POST_USERS_URL . '=' . $profiledata['user_id'])), E_USER_ERROR);

}
elseif ($action == 'edit')
{
	$sql = 'SELECT friend_id
		FROM ' . FRIENDS_TABLE . '
		WHERE friend_id = ' . (int) $f;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询好友信息', E_USER_WARNING);
	}

	if (!$db->sql_numrows($result))
	{
		trigger_error('他不是你的好友', E_USER_ERROR);
	}

	$submit = (isset($_POST['submit'])) ? true :false;

	if (!$submit)
	{
		trigger_error('
			<p>请输入备注</p>
			<form action="' . append_sid('ucp.php?mode=friends&action=edit&' . POST_USERS_URL . '=' . $profiledata['user_id'] . '&f=' . $f) . '" method="post">
				<input type="text" name="remark" value="" maxlength="12" />
				<input type="submit" name="submit" value="修改" />
			</form>',
			E_USER_ERROR);
	}
	
	$remark = get_var('remark', '');

	if (strlen($remark) > 25 || strlen($remark) < 1)
	{
		trigger_error('备注不合法' . back_link(append_sid('ucp.php?mode=friends&action=edit&' . POST_USERS_URL . '=' . $profiledata['user_id'] . '&f=' . $f)), E_USER_ERROR);
	}

	$sql = 'UPDATE ' . FRIENDS_TABLE . "
		SET remark = '$remark'
		WHERE friend_id = " . (int) $f;

	if (!$db->sql_query($sql))
	{
		trigger_error('无法修改备注', E_USER_WARNING);
	}

	trigger_error('备注修改成功' . back_link(append_sid('ucp.php?mode=friends&' . POST_USERS_URL . '=' . $profiledata['user_id'])), E_USER_ERROR);
}

$per = 10;
$start = get_pagination_start($per);

$sql = 'SELECT f.friend_id, f.user_id, f.remark, u.user_nic_color, u.user_allow_viewonline, u.user_avatar_type, u.user_allowavatar, u.user_avatar, u.user_sig, u.user_session_time
	FROM ' . FRIENDS_TABLE . ' f, ' . USERS_TABLE . ' u
	WHERE f.ucp_id = ' . $profiledata['user_id'] . '
		AND u.user_id = f.user_id
	LIMIT ' . $start . ' , ' . $per;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询好友信息', E_USER_WARNING);
}

if (!$db->sql_numrows($result))
{
	$template->assign_block_vars('not_friend', array());
}
else
{
	$i = 0;
	while ($row = $db->sql_fetchrow($result))
	{

		if ($row['user_session_time'] >= (time()-$board_config['online_time']))
		{
			if ($row['user_allow_viewonline'])
			{
				$online_class = '';
			}
			else if ( $profiledata['user_id'] == $row['user_id'] )
			{
				$online_class = 'class = "avatar"';
			}
			else
			{
				$online_class = 'class = "avatar"';
			}
		}
		else
		{
			$online_class = 'class = "avatar"';
		}

		$avatar_img = ''; 
		if ( $row['user_avatar_type'] && $row['user_allowavatar'] ) 
		{ 
			switch( $row['user_avatar_type'] ) 
			{ 
				case USER_AVATAR_UPLOAD: 
					$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $row['user_avatar'] . '" ' . $online_class . ' style="float:left;" title="' . $nomer_posta . '" alt="." width="48" hight="48" />' : make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class . ' style="float:left;"'); 
				break; 
				case USER_AVATAR_REMOTE: 
					$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $row['user_avatar'] . '" ' . $online_class . ' style="float:left;" alt="." title="' . $nomer_posta . '" width="48" hight="48" />' : make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class .' style="float:left;"'); 
				break; 
				default:
					$avatar_img = make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class . ' style="float:left;"');
			} 
		}
		else
		{
			$avatar_img = make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class . ' style="float:left;"');
		}

		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

		$template->assign_block_vars('friends', array(
			'ROW_CLASS'	=>$row_class,
			'REMARK' 	=> $row['remark'],
			'AVATAR'	=> $avatar_img,
			'USER'		=> '<span style="color:' . $row['user_nic_color']  . '">' . $row['remark'] .'</span>',
			'U_UCP'		=> append_sid("ucp.php?mode=main&amp;" . POST_USERS_URL . '='  . $row['user_id']),
			'U_EDIT'	=> append_sid('ucp.php?mode=friends&action=edit&' . POST_USERS_URL . '=' . $profiledata['user_id'] . '&f=' . $row['friend_id']),
			'U_DELETE'	=> append_sid('ucp.php?mode=friends&action=del&' . POST_USERS_URL . '=' . $profiledata['user_id'] . '&f=' . $row['friend_id']))
		);
		$i++;
	}
}

$sql = 'SELECT count(friend_id) AS total
	FROM ' . FRIENDS_TABLE . '
	WHERE ucp_id = ' . $profiledata['user_id'];

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法统计好友', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

$total_friends = $row['total'];

$pagination = generate_pagination('ucp.php?mode=friends&' . POST_USERS_URL . '=' . $profiledata['user_id'], $total_friends, $per, $start);

page_header('我的好友');

$template->set_filenames(array(
	'body' => 'ucp/friends_body.tpl')
);

$template->assign_vars(array(
	'U_BACK'			=> append_sid('ucp.php?mode=manage&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'PAGINATION'		=> $pagination,
	'S_FRIEND_ACTION' 	=> append_sid('ucp.php?mode=friends&action=add&' . POST_USERS_URL . '=' . $profiledata['user_id']))
);

$template->pparse('body');

page_footer();
?>