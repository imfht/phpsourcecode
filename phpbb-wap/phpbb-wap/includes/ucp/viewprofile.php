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

//统计用户附件
$sql = 'SELECT count(*) AS total
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE user_id_1 = '.$profiledata['user_id'].' AND user_id_2 = 0';
	
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Unable to get attachment information.', E_USER_WARNING);
}
$rowatt = $db->sql_fetchrow($result);

if ( $rowatt['total'] > 0 )
{
	$totalfiles = '<a href="' . append_sid('ucp.php?mode=viewfiles&amp;' . POST_USERS_URL .'=' . $profiledata['user_id']) . '">' . $rowatt['total'] . '</a>';
}
else
{
	$totalfiles = 0;
}

//得出用户等级
$sql = 'SELECT *
	FROM ' . RANKS_TABLE . ' 
	ORDER BY rank_special, rank_min';
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain ranks information', E_USER_WARNING);
}

$ranksrow = array();

while ( $row = $db->sql_fetchrow($result) )
{
	$ranksrow[] = $row;
}
$db->sql_freeresult($result);

$user_rank = '';
if ( !empty($profiledata['user_zvanie']) )
{
	$user_rank = $profiledata['user_zvanie'];
}
else
{
	if ( $profiledata['user_rank'] )
	{
		for($i = 0; $i < count($ranksrow); $i++)
		{
			if ( $profiledata['user_rank'] == $ranksrow[$i]['rank_id'] && $ranksrow[$i]['rank_special'] )
			{
				$user_rank = $ranksrow[$i]['rank_title'];
			}
		}
	}
	else
	{
		for($i = 0; $i < count($ranksrow); $i++)
		{
			if ( $profiledata['user_posts'] >= $ranksrow[$i]['rank_min'] && !$ranksrow[$i]['rank_special'] )
			{
				$user_rank = $ranksrow[$i]['rank_title'];
			}
		}
	}
}

// 个性头像
$avatar_img = '';
if ( $profiledata['user_avatar_type'] && $profiledata['user_allowavatar'] )
{
	switch( $profiledata['user_avatar_type'] )
	{
		case USER_AVATAR_UPLOAD:
			$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0" /><br />' : '';
			break;
		case USER_AVATAR_REMOTE:
			$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $profiledata['user_avatar'] . '" alt="" border="0" /><br />' : '';
			break;
		case USER_AVATAR_GALLERY:
			$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0" /><br />' : '';
			break;
	}
}

$email = '';
if ( ($userdata['session_logged_in'] && !empty($profiledata['user_viewemail'])) || $userdata['user_level'] == ADMIN )
{
	$template->assign_block_vars('email', array());
	$email_uri = ( $board_config['board_email_form'] ) ? append_sid('ucp.php?mode=email&amp;' . POST_USERS_URL .'=' . $profiledata['user_id']) : 'mailto:' . $profiledata['user_email'];
	$email = '<a href="' . $email_uri . '">' . $profiledata['user_email'] . '</a>';
}

$www = '';
if ( !empty($profiledata['user_website']) ) 
{ 
	$template->assign_block_vars('www', array());
	$www = '<a href="' . $profiledata['user_website'] . '">' . $profiledata['user_website'] . '</a>';
}

$signature = '';
if ( !empty($profiledata['user_sig']) ) 
{ 
	$template->assign_block_vars('signature', array());
	$signature = $profiledata['user_sig'];
}

$qq = '';
if ( !empty($profiledata['user_qq']) ) 
{ 
	$template->assign_block_vars('qq', array());
	$qq = $profiledata['user_qq'];
}

$number = '';
if ( !empty($profiledata['user_number']) ) 
{ 
	$template->assign_block_vars('number', array());
	$number = $profiledata['user_number'];
}

$aim = '';
if ( !empty($profiledata['user_aim']) ) 
{ 
	$template->assign_block_vars('aim', array());
	$aim = $profiledata['user_aim'];
}

$msn = '';
if ( !empty($profiledata['user_msnm']) ) 
{ 
	$template->assign_block_vars('msn', array());
	$msn = $profiledata['user_msnm'];
}

$yim = '';
if ( !empty($profiledata['user_yim']) ) 
{ 
	$template->assign_block_vars('yim', array());
	$yim = $profiledata['user_yim'];
}

$from = '';
if ( !empty($profiledata['user_from']) ) 
{ 
	$template->assign_block_vars('from', array());
	$from = $profiledata['user_from'];
}

$occ = '';
if ( !empty($profiledata['user_occ']) ) 
{ 
	$template->assign_block_vars('occ', array());
	$occ = $profiledata['user_occ'];
}

$interests = '';
if ( !empty($profiledata['user_interests']) ) 
{ 
	$template->assign_block_vars('interests', array());
	$interests = $profiledata['user_interests'];
}

$this_year = create_date('Y', time(), $board_config['board_timezone']);
$this_date = create_date('md', time(), $board_config['board_timezone']);

//星座
$zodiacs = array(
	'zodiacdates' => array (
		'0101', '0120', '0121', '0219', '0220', '0320', '0321', '0420',
		'0421', '0520', '0521', '0621', '0622', '0722', '0723', '0823',
		'0824', '0922', '0923', '1022', '1023', '1122', '1123', '1221',
		'1222', '1231'
	),
	'name' => array (
		'摩羯座', '水瓶座', '双鱼座', '白羊座', '金牛座',
		'双子座', '巨蟹座', '狮子座', '处女座', '天秤座',
		'天蝎座', '射手座', '摩羯座'
	)
);

$zodiac = $user_birthday = $userbirthdate = $u_zodiac = '';
if ( $profiledata['user_birthday'] != 999999 )
{
	$user_birthdate = realdate('md', $profiledata['user_birthday']);
	$i = 0;
	while ($i < 26)
	{
		if ($user_birthdate >= $zodiacs['zodiacdates'][$i] && $user_birthdate <= $zodiacs['zodiacdates'][$i+1])
		{
			$zodiac 	= $zodiacs['name'][($i/2)];
			//$u_zodiac 	= $images[$zodiacs[($i/2)]];
			$i 			= 26;
		}
		else
		{
			$i = $i+2;
		}
	}
	
	$template->assign_block_vars('birthday', array());
	
	$user_birthday 	= realdate('Y年m月d日', $profiledata['user_birthday']);
	$userbirthdate 	= '';
	$userbdate 		= realdate('md', $profiledata['user_birthday']);
	$userbirthdate 	= $this_year - realdate ('Y',$profiledata['user_birthday']);
	if ( $this_date < $userbdate )
	{
		$userbirthdate--;
	}
	$userbirthdate = $userbirthdate;
} 

$gender = '';
if ( !empty($profiledata['user_gender']) ) 
{ 
	$template->assign_block_vars('gender', array());
	switch ($profiledata['user_gender']) 
	{
		case 1:
			$gender = '男';
		break; 
		case 2:
			$gender = '女';
		break; 
		default:
			$gender = '未知'; 
	}
}

if (function_exists('get_html_translation_table'))
{
	$u_search_author = urlencode(strtr($profiledata['username'], array_flip(get_html_translation_table(HTML_ENTITIES))));
}
else
{
	$u_search_author = urlencode(str_replace(array('&amp;', '&#039;', '&quot;', '&lt;', '&gt;'), array('&', "'", '"', '<', '>'), $profiledata['username']));
}

$sql = 'SELECT COUNT(topic_id) AS total 
	FROM ' . TOPICS_TABLE . ' 
	WHERE topic_poster = ' . $profiledata['user_id'];
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain matched topics list', E_USER_WARNING);
}
$row = $db->sql_fetchrow($result);
$total_topics = $row['total'];

$sql = 'SELECT u.user_id, u.username, u.user_level, g.group_id, g.group_name, g.group_single_user, ug.user_pending
	FROM ' . USERS_TABLE . ' u, ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug 
	WHERE u.user_id = ' . $profiledata['user_id'] . ' AND ug.user_id = u.user_id AND g.group_id = ug.group_id AND ug.user_pending = 0';
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Couldn\'t obtain user/group information', E_USER_WARNING);
}
$ug_info = array();
while( $row = $db->sql_fetchrow($result) )
{
	$ug_info[] = $row;
}
$db->sql_freeresult($result);

$name = array();
$id = array();
for($i = 0; $i < count($ug_info); $i++)
{
	if( !$ug_info[$i]['group_single_user'] )
	{
		$name[] = $ug_info[$i]['group_name'];
		$id[] = intval($ug_info[$i]['group_id']);
	}
}

$t_usergroup_list = '';
if( count($name) )
{
	for($i = 0; $i < (count($ug_info) - 1); $i++)
	{
		if (!$ug_info[$i]['user_pending'])
		{
			$t_usergroup_list .= '- <a href="' . append_sid('groupcp.php?g=' . $id[$i]) . '">' . $name[$i] . '</a><br/>';
		}
	}
}

$user_delete 	= ( ($userdata['user_level'] == ADMIN) && ($profiledata['user_level'] == USER) ) ? '<br />【<a href="' . append_sid('ucp.php?mode=delete&amp;u=' . $profiledata['user_id']) . '">删</a>】' : '';
$user_clone 	= ( $userdata['user_level'] == ADMIN ) ? '【<a href="' . append_sid('ucp.php?mode=clone&amp;u=' . $profiledata['user_id']) . '">同</a>】' : '';
$user_edit 		= ( $userdata['user_level'] == ADMIN ) ? '【<a href="' . append_sid('admin/admin_users.php?mode=edit&amp;'. POST_USERS_URL. '='. $profiledata['user_id']. '&amp;sid='. $userdata['session_id']) . '">改</a>】' : '';
$user_ban		= ( $userdata['user_level'] == ADMIN ) ? '【<a href="' . append_sid('ucp.php?mode=ban&amp;'. POST_USERS_URL. '='. $profiledata['user_id']) . '">黑</a>】' : '';

if ($userdata['user_level'] == ADMIN && $profiledata['user_level'] == USER)
{
	if ($profiledata['user_active'] == 1)
	{
		$link_lock = '【<a href="' . append_sid('ucp.php?mode=lock&amp;u=' . $profiledata['user_id']) . '">停</a>】';
	}
	elseif ($profiledata['user_active'] == 0)
	{
		$link_lock = '【<a href="' . append_sid('ucp.php?mode=lock&amp;u=' . $profiledata['user_id']) . '">开</a>】';
	}
}
else
{
	$link_lock = '';
}

if ( $userdata['user_id'] == $profiledata['user_id'] )
{
	$template->assign_block_vars('editprofile', array() );
}
if ( $userdata['user_points'] > 0 && $userdata['user_id'] != $profiledata['user_id'] )
{
	$template->assign_block_vars('money', array() );
}
if ( $t_usergroup_list != '' )
{
	$template->assign_block_vars('usergroup', array() );
}

if ($userdata['user_id'] == $profiledata['user_id'] || $userdata['user_level'] == ADMIN)
{
	$template->assign_block_vars('manage', array());
}

if ($profiledata['user_id'] == $userdata['user_id'] || $userdata['user_level'] == ADMIN)
{
	$sql = 'SELECT COUNT(tc_id) AS total_tc
		FROM ' . TOPIC_COLLECT_TABLE . '
		WHERE tc_user = ' . $profiledata['user_id'];

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询帖子收藏数据', E_USER_WARNING);
	}

	$tcrow = $db->sql_fetchrow($result);

	$template->assign_block_vars('topic_collect', array(
		'TOTAL' => $tcrow['total_tc'],
		'U_VIEW' => append_sid('ucp.php?mode=topic_collect&' . POST_USERS_URL . '=' . $profiledata['user_id']))
	);
}


$page_title = '浏览用户个人资料';

page_header($page_title);

page_jump();

$template->set_filenames(array(
	'body' => 'ucp/ucp_body.tpl')
);

$template_image_array = obtain_images_data();

$template->assign_vars(array(
	'USERNAME' 				=> $profiledata['username'],
	'USER_ID'				=> $profiledata['user_id'],
	'USERGROUP' 			=> $t_usergroup_list,
	'JOINED' 				=> create_date($profiledata['user_dateformat'], $profiledata['user_regdate'], $board_config['board_timezone']),
	'LASTVISIT' 			=> ( $profiledata['user_lastvisit'] != 0 ) ? create_date($profiledata['user_dateformat'], $profiledata['user_lastvisit'], $board_config['board_timezone']) : '账户未激活',
	'USER_RANK' 			=> $user_rank,
	
	'POSTS' 				=> $profiledata['user_posts'],
	'TOPICS' 				=> $total_topics,
	'MONEY' 				=> $profiledata['user_points'],
	'ATTACH' 				=> $totalfiles,
	
	'USER_DELETE' 			=> $user_delete,
	'USER_CLONE' 			=> $user_clone,
	'USER_LOCK' 			=> $link_lock,
	'USER_BAN'				=> $user_ban,
	'U_ADMIN_PROFILE' 		=> $user_edit,

	'POINTS_NAME' 			=> $board_config['points_name'],
	
	
	'EMAIL' 				=> $email,
	'WWW' 					=> $www,
	'SIGNATURE'				=> $signature,
	'QQ' 					=> $qq, 
	'NUMBER' 				=> $number, 
	'AIM' 					=> $aim,
	'MSN' 					=> $msn,
	'YIM' 					=> $yim,
	'LOCATION' 				=> $from,
	'OCCUPATION' 			=> $occ,
	'INTERESTS' 			=> $interests,
    'GENDER' 				=> $gender, 
	'BIRTHDAY' 				=> $user_birthday,
	'ZODIAC' 				=> $zodiac,
	'USER_AGE' 				=> $userbirthdate,
	'U_ZODIAC' 				=> $u_zodiac,
	'AVATAR_IMG' 			=> $avatar_img,

	'VIEWING_PROFILE' 		=> sprintf('正在浏览 %s 的个人资料', $profiledata['username']),
	
	'IMG_PM'				=> make_style_image('privmsg_create', '发信息给他'),
	'IMG_TOP'				=> make_style_image('top'),

	'U_ADD_FRIEND'			=> append_sid('ucp.php?mode=friends&action=add&' . POST_USERS_URL . '=' . $userdata['user_id'] . '&f=' . $profiledata['user_id']),
	'U_UCP_MAIN'			=> append_sid('ucp.php?mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_VIEWPROFILE'			=> append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_UCP_MANAGE'			=> append_sid('ucp.php?mode=manage&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_PM' 					=> append_sid('privmsg.php?mode=post&amp;' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_GUESTBOOK'			=> append_sid('ucp.php?mode=guestbook&amp;' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_SEARCH_USER' 		=> append_sid('search.php?search_author=' . $u_search_author . '&amp;ucp'),
	'U_SEARCH_USER_TOPICS' 	=> append_sid('search.php?search_author=' . $u_search_author . '&amp;mode=all_topics&amp;ucp'),
	'U_MONEY_SEND' 			=> append_sid('ucp.php?mode=money&amp;' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_ALBUM'				=> append_sid('album.php'),
	'S_PROFILE_ACTION' 		=> append_sid('ucp.php'))
);

$template->pparse('body');
page_footer();
?>