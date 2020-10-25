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

$album_root_path = ROOT_PATH . 'album/';

include(ROOT_PATH . 'common.php');

include(ROOT_PATH . 'includes/functions/validate.php');
include(ROOT_PATH . 'includes/functions/bbcode.php');

$userdata = $session->start($user_ip, PAGE_ALBUM);
init_userprefs($userdata);

include(ROOT_PATH . 'includes/album/common.php');

if ( isset($_GET['action']) || isset($_POST['action']) )
{
	$action = ( isset($_POST['action']) ) ? htmlspecialchars($_POST['action']) : htmlspecialchars($_GET['action']);
}
else
{
	$action = '';
}

if ( $action == 'cat' )
{
	if( isset($_POST['cat_id']) )
	{
		$cat_id = intval($_POST['cat_id']);
	}
	else if( isset($_GET['cat_id']) )
	{
		$cat_id = intval($_GET['cat_id']);
	}
	else
	{
		trigger_error('没有指定类别');
	}

	if ($cat_id == PERSONAL_GALLERY)
	{
		redirect(append_sid("album.php?action=personal"));
	}

	$sql = "SELECT c.*, COUNT(p.pic_id) AS count
		FROM ". ALBUM_CAT_TABLE ." AS c LEFT JOIN ". ALBUM_TABLE ." AS p ON c.cat_id = p.pic_cat_id
		WHERE c.cat_id <> 0
		GROUP BY c.cat_id
		ORDER BY cat_order";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法查询分类信息', E_USER_WARNING);
	}

	$thiscat = array();
	$catrows = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$album_user_access = album_user_access($row['cat_id'], $row, 1, 0, 0, 0, 0, 0); // VIEW
		if ($album_user_access['view'] == 1)
		{
			$catrows[] = $row;

			if( $row['cat_id'] == $cat_id )
			{
				$thiscat = $row;
				$auth_data = album_user_access($cat_id, $row, 1, 1, 1, 1, 1, 1); // ALL
				$total_pics = $thiscat['count'];
			}
		}
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	if( !$auth_data['view'] )
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=cat&cat_id=$cat_id");	
		}
		else
		{
			trigger_error('没有权限查看', E_USER_ERROR);
		}
	}

	$auth_key = array_keys($auth_data);
	$lang = array(
		'Album_view_can' => '你 <b>可以</b> 查看这类别的图像',
		'Album_view_cannot' => '你 <b>不可以</b> 查看这类别的图像',
		'Album_upload_can' => '你 <b>可以</b> 添加这类别的图像',
		'Album_upload_cannot' => '您 <b>不可以</b> 添加这类别的图像', 
		'Album_rate_can' => '你 <b>可以</b> 评价这一类别的图像 ',
		'Album_rate_cannot' => '你 <b>不可以</b> 评价这一类别的图像', 
		'Album_comment_can' => '你 <b>可以</b> 在图像上评论这一类',
		'Album_comment_cannot' => '你 <b>不可以</b> 在图像上评论这一类', 
		'Album_edit_can' => '你 <b>可以</b> 编辑你的照片在这个类别和评论', 
		'Album_edit_cannot' => '你 <b>不可以</b> 编辑你的照片在这个类别和评论', 
		'Album_delete_can' => '你 <b>可以</b> 在这个类别中删除你的照片和评论',
		'Album_delete_cannot' => '你 <b>不可以</b> 在这个类别中删除你的照片和评论', 
		'Album_moderate_can' => '你 <b>可以</b> %s管理%s 这一类别'
	);

	$auth_list = '';
	for ($i = 0; $i < (count($auth_data) - 1); $i++)
	{
		if( ( ($album_config['rate'] == 0) and ($auth_key[$i] == 'rate') ) or ( ($album_config['comment'] == 0) and ($auth_key[$i] == 'comment') ) )
		{
			continue;
		}

		$auth_list .= ($auth_data[$auth_key[$i]] == 1) ? $lang['Album_'. $auth_key[$i] .'_can'] : $lang['Album_'. $auth_key[$i] .'_cannot'];
		$auth_list .= '<br />';
	}

	if( ($userdata['user_level'] == ADMIN) or ($auth_data['moderator'] == 1) )
	{
		$auth_list .= sprintf($lang['Album_moderate_can'], '<a href="'. append_sid("album.php?action=modcp&amp;cat_id=$cat_id") .'">', '</a>');
		$moderka = '<a href="'. append_sid("album.php?action=modcp&amp;cat_id=$cat_id") .'">管理该分类</a>';
	}

	$grouprows = array();
	$moderators_list = '';

	if ($thiscat['cat_moderator_groups'] != '')
	{
		$sql = "SELECT group_id, group_name, group_type, group_single_user
			FROM " . GROUPS_TABLE . "
			WHERE group_single_user <> 1
				AND group_type <> ". GROUP_HIDDEN ."
				AND group_id IN (". $thiscat['cat_moderator_groups'] .")
			ORDER BY group_name ASC";
		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法获取小组信息', E_USER_WARNING);
		}

		while( $row = $db->sql_fetchrow($result) )
		{
			$grouprows[] = $row;
		}

		if( count($grouprows) > 0 )
		{
			for ($j = 0; $j < count($grouprows); $j++)
			{
				$group_link = '<a href="'. append_sid("groupcp.php?". POST_GROUPS_URL .'='. $grouprows[$j]['group_id']) .'">'. $grouprows[$j]['group_name'] .'</a>';
				$moderators_list .= ($moderators_list == '') ? $group_link : ', ' . $group_link;
			}
		}
	}

	if( empty($moderators_list) )
	{
		$moderators_list = '无';
	}
	$pics_per_page = $album_config['rows_per_page'] * $album_config['cols_per_page'];
	if ( isset($_POST['start1']) )
	{
		$start1 = abs(intval($_POST['start1']));
		$start1 = ($start1 < 1) ? 1 : $start1;
		$start = (($start1 - 1) * $pics_per_page);
	}
	else
	{
		if( isset($_GET['start']) )
		{
			$start = intval($_GET['start']);
		}
		else if( isset($_POST['start']) )
		{
			$start = intval($_POST['start']);
		}
		else
		{
			$start = 0;
		}
		$start = ($start < 0) ? 0 : $start;
	}

	if( isset($_GET['sort_method']) )
	{
		switch ($_GET['sort_method'])
		{
			case 'pic_time':
				$sort_method = 'p.pic_time';
				break;
			case 'pic_title':
				$sort_method = 'p.pic_title';
				break;
			case 'username':
				$sort_method = 'u.username';
				break;
			case 'pic_view_count':
				$sort_method = 'p.pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = $album_config['sort_method'];
		}
	}
	else if( isset($_POST['sort_method']) )
	{
		switch ($_POST['sort_method'])
		{
			case 'pic_time':
				$sort_method = 'p.pic_time';
				break;
			case 'pic_title':
				$sort_method = 'p.pic_title';
				break;
			case 'username':
				$sort_method = 'u.username';
				break;
			case 'pic_view_count':
				$sort_method = 'p.pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = $album_config['sort_method'];
		}
	}
	else
	{
		$sort_method = $album_config['sort_method'];
	}

	if( isset($_GET['sort_order']) )
	{
		switch ($_GET['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			case 'DESC':
				$sort_order = 'DESC';
				break;
			default:
				$sort_order = $album_config['sort_order'];
		}
	}
	else if( isset($_POST['sort_order']) )
	{
		switch ($_POST['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			case 'DESC':
				$sort_order = 'DESC';
				break;
			default:
				$sort_order = $album_config['sort_order'];
		}
	}
	else
	{
		$sort_order = $album_config['sort_order'];
	}

	if ($total_pics > 0)
	{
		$limit_sql = ($start == 0) ? $pics_per_page : $start .','. $pics_per_page;

		$pic_approval_sql = 'AND p.pic_approval = 1';
		if ($thiscat['cat_approval'] != ALBUM_USER)
		{
			if( ($userdata['user_level'] == ADMIN) or (($auth_data['moderator'] == 1) and ($thiscat['cat_approval'] == ALBUM_MOD)) )
			{
				$pic_approval_sql = '';
			}
		}

		$sql = "SELECT p.pic_id, p.pic_title, p.pic_desc, p.pic_user_id, p.pic_user_ip, p.pic_username, p.pic_time, p.pic_cat_id, p.pic_view_count, p.pic_lock, p.pic_approval, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments, MAX(c.comment_id) as new_comment
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
				LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
			WHERE p.pic_cat_id = '$cat_id' $pic_approval_sql
			GROUP BY p.pic_id
			ORDER BY $sort_method $sort_order
			LIMIT $limit_sql";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法查询图片信息', E_USER_WARNING);
		}

		$picrow = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			$picrow[] = $row;
		}


		for ($i = 0; $i < count($picrow); $i += $album_config['cols_per_page'])
		{
			for ($j = $i; $j < ($i + $album_config['cols_per_page']); $j++)
			{
				if( $j >= count($picrow) )
				{
					break;
				}

				if(!$picrow[$j]['rating'])
				{
					$picrow[$j]['rating'] = '无';
				}
				else
				{
					$picrow[$j]['rating'] = round($picrow[$j]['rating'], 2);
				}

				$approval_link = '';
				if ($thiscat['cat_approval'] != ALBUM_USER)
				{
					if( ($userdata['user_level'] == ADMIN) or (($auth_data['moderator'] == 1) and ($thiscat['cat_approval'] == ALBUM_MOD)) )
					{
						$approval_mode = ($picrow[$j]['pic_approval'] == 0) ? 'approval' : 'unapproval';
						$approval_link .= '<a href="'. append_sid("album.php?action=modcp&amp;mode=$approval_mode&amp;pic_id=". $picrow[$j]['pic_id']) .'">';
						$approval_link .= ($picrow[$j]['pic_approval'] == 0) ? '<b>通过审核</b>' : '拒绝通过';
						$approval_link .= '</a><br/>';
					}
				}

				if( ($picrow[$j]['user_id'] == ALBUM_GUEST) or ($picrow[$j]['username'] == '') )
				{
					$pic_poster = ($picrow[$j]['pic_username'] == '') ? '匿名用户' : $picrow[$j]['pic_username'];
				}
				else
				{
					$pic_poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $picrow[$j]['user_id']) .'">'. $picrow[$j]['username'] .'</a>';
				}

				$row_class = ( !($j % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('picrow', array(
					'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.php?action=pic&amp;pic_id=". $picrow[$j]['pic_id']) : append_sid("album.php?action=page&amp;pic_id=". $picrow[$j]['pic_id']),
					'TITLE' => $picrow[$j]['pic_title'],
					'ROW_CLASS' => $row_class,
					'POSTER' => $pic_poster,
					'TIME' => create_date($board_config['default_dateformat'], $picrow[$j]['pic_time'], $board_config['board_timezone']),
					'VIEW' => $picrow[$j]['pic_view_count'],
					'RATING' => ($album_config['rate'] == 1) ? ( '<a href="'. append_sid("album.php?action=rate&amp;pic_id=". $picrow[$j]['pic_id']) . '">评价</a>: ' . $picrow[$j]['rating'] . '<br />') : '',
					'COMMENTS' => ($album_config['comment'] == 1) ? ( '<a href="'. append_sid("album.php?action=comment&amp;pic_id=". $picrow[$j]['pic_id']) . '">评论</a>: ' . $picrow[$j]['comments'] . '<br />') : '',
					'EDIT' => ( ( $auth_data['edit'] and ($picrow[$j]['pic_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_edit_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.php?action=edit&amp;pic_id=". $picrow[$j]['pic_id']) . '">编辑</a>|' : '',
					'DELETE' => ( ( $auth_data['delete'] and ($picrow[$j]['pic_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_delete_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.php?action=delete&amp;pic_id=". $picrow[$j]['pic_id']) . '">删除</a>|' : '',
					'MOVE' => ($auth_data['moderator']) ? '<a href="'. append_sid("album.php?action=modcp&amp;mode=move&amp;pic_id=". $picrow[$j]['pic_id']) .'">移动</a>' : '',
					'LOCK' => ($auth_data['moderator']) ? '<a href="'. append_sid("album.php?action=modcp&amp;mode=". (($picrow[$j]['pic_lock'] == 0) ? 'lock' : 'unlock') ."&amp;pic_id=". $picrow[$j]['pic_id']) .'">'. (($picrow[$j]['pic_lock'] == 0) ? '锁定' : '解锁') .'</a>|' : '',
					'IP' => ($userdata['user_level'] == ADMIN) ? 'IP地址: ' . decode_ip($picrow[$j]['pic_user_ip']) .'<br />' : ''
					)
				);

				$template->assign_block_vars('picrow.piccol', array(
					'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.php?action=pic&amp;pic_id=". $picrow[$j]['pic_id']) : append_sid("album.php?action=page&amp;pic_id=". $picrow[$j]['pic_id']),
					'THUMBNAIL' => append_sid("album.php?action=thumbnail&amp;pic_id=". $picrow[$j]['pic_id']),
					'DESC' => $picrow[$j]['pic_desc'],
					'APPROVAL' => $approval_link,
					)
				);
			}
		}

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination(append_sid("album.php?action=cat&amp;cat_id=$cat_id&amp;sort_method=$sort_method&amp;sort_order=$sort_order"), $total_pics, $pics_per_page, $start))
		);
	}
	else
	{
		$template->assign_block_vars('no_pics', array());
	}

	$album_jumpbox = '<form name="jumpbox" action="'. append_sid("album.php?action=cat") .'" method="post">';
	$album_jumpbox .= '跳转到:&nbsp;<select name="cat_id">';
	for ($i = 0; $i < count($catrows); $i++)
	{
		$album_jumpbox .= '<option value="'. $catrows[$i]['cat_id'] .'"';
		$album_jumpbox .= ($catrows[$i]['cat_id'] == $cat_id) ? 'selected="selected"' : '';
		$album_jumpbox .= '>' . $catrows[$i]['cat_title'] .'</option>';
	}
	$album_jumpbox .= '</select>';
	$album_jumpbox .= '&nbsp;<input type="submit" value="确定" />';
	$album_jumpbox .= '<input type="hidden" name="sid" value="'. $userdata['session_id'] .'" />';
	$album_jumpbox .= '</form>';

	$sort_rating_option = '';
	$sort_comments_option = '';
	if( $album_config['rate'] == 1 )
	{
		$sort_rating_option = '<option value="rating" ';
		$sort_rating_option .= ($sort_method == 'rating') ? 'selected="selected"' : '';
		$sort_rating_option .= '>评价</option>';
	}
	if( $album_config['comment'] == 1 )
	{
		$sort_comments_option = '<option value="comments" ';
		$sort_comments_option .= ($sort_method == 'comments') ? 'selected="selected"' : '';
		$sort_comments_option .= '>评论</option>';
		$sort_new_comment_option = '<option value="new_comment" ';
		$sort_new_comment_option .= ($sort_method == 'new_comment') ? 'selected="selected"' : '';
		$sort_new_comment_option .= '>评论</option>';
	}

	page_header('图片分类');

	$template->set_filenames(array(
		'body' => 'album/cat_body.tpl')
	);

	$template->assign_vars(array(
		'U_VIEW_CAT' => append_sid("album.php?action=cat&amp;cat_id=$cat_id"),
		'CAT_TITLE' => $thiscat['cat_title'],
		'MODERATORS' => $moderators_list,
		'U_UPLOAD_PIC' => append_sid("album.php?action=upload&amp;cat_id=$cat_id"),
		'S_COLS' => $album_config['cols_per_page'],
		'S_COL_WIDTH' => (100/$album_config['cols_per_page']) . '%',
		'ALBUM_JUMPBOX' => $album_jumpbox,
		'S_ALBUM_ACTION' => append_sid("album.php?action=cat&amp;cat_id=$cat_id"),
		'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
		'SORT_TIME' => ($sort_method == 'pic_time') ? 'selected="selected"' : '',
		'SORT_PIC_TITLE' => ($sort_method == 'pic_title') ? 'selected="selected"' : '',
		'SORT_USERNAME' => ($sort_method == 'pic_user_id') ? 'selected="selected"' : '',
		'SORT_VIEW' => ($sort_method == 'pic_view_count') ? 'selected="selected"' : '',
		'SORT_RATING_OPTION' => $sort_rating_option,
		'SORT_COMMENTS_OPTION' => $sort_comments_option,
		'SORT_NEW_COMMENT_OPTION' => $sort_new_comment_option,
		'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
		'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : '',
		'U_MODERKA' => $moderka,
		'S_AUTH_LIST' => $auth_list)
	);

	$template->pparse('body');

	page_footer();

}
elseif ( $action == 'comment' )
{

	if( $album_config['comment'] == 0 )
	{
		trigger_error('没有权限查看', E_USER_ERROR);
	}

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		if( isset($_GET['comment_id']) )
		{
			$comment_id = intval($_GET['comment_id']);
		}
		else if( isset($_POST['comment_id']) )
		{
			$comment_id = intval($_POST['comment_id']);
		}
		else
		{
			trigger_error('错误的请求', E_USER_ERROR);
		}
	}

	if( isset($comment_id) )
	{
		$sql = "SELECT comment_id, comment_pic_id
				FROM ". ALBUM_COMMENT_TABLE ."
				WHERE comment_id = '$comment_id'";

		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query comment and pic information', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		if( empty($row) )
		{
			trigger_error('This comment does not exist', E_USER_ERROR);
		}

		$pic_id = $row['comment_pic_id'];
	}

	$sql = "SELECT p.*, u.user_id, u.username, COUNT(c.comment_id) as comments_count
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
			WHERE pic_id = '$pic_id'
			GROUP BY p.pic_id
			LIMIT 1";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$total_comments = $thispic['comments_count'];
	$comments_per_page = $board_config['posts_per_page'];

	if( empty($thispic) )
	{
		trigger_error('图片[' . $pic_id . ']不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$auth_data = album_user_access($cat_id, $thiscat, 1, 0, 0, 1, 1, 1);

	if ($auth_data['view'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=comment&pic_id=$pic_id");
			exit;
		}
		else
		{
			trigger_error('没有查看权限', E_USER_ERROR);
		}
	}

	if( !isset($_POST['comment']) )
	{
		if( !isset($comment_id) )
		{
			if ( isset($_POST['start1']) )
			{
				$start1 = abs(intval($_POST['start1']));
				$start1 = ($start1 < 1) ? 1 : $start1;
				$start = (($start1 - 1) * $board_config['topics_per_page']);
			}
			else
			{
				if( isset($_GET['start']) )
				{
					$start = intval($_GET['start']);
				}
				else if( isset($_POST['start']) )
				{
					$start = intval($_POST['start']);
				}
				else
				{
					$start = 0;
				}
				$start = ($start < 0) ? 0 : $start;
			}
		}
		else
		{
			$sql = "SELECT COUNT(comment_id) AS count
					FROM ". ALBUM_COMMENT_TABLE ."
					WHERE comment_pic_id = $pic_id
						AND comment_id < $comment_id";

			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain comments information from the database', E_USER_WARNING);
			}

			$row = $db->sql_fetchrow($result);

			if( !empty($row) )
			{
				$start = floor( $row['count'] / $comments_per_page ) * $comments_per_page;
			}
			else
			{
				$start = 0;
			}
		}

		if( isset($_GET['sort_order']) )
		{
			switch ($_GET['sort_order'])
			{
				case 'ASC':
					$sort_order = 'ASC';
					break;
				default:
					$sort_order = 'DESC';
			}
		}
		else if( isset($_POST['sort_order']) )
		{
			switch ($_POST['sort_order'])
			{
				case 'ASC':
					$sort_order = 'ASC';
					break;
				default:
					$sort_order = 'DESC';
			}
		}
		else
		{
			$sort_order = 'ASC';
		}

		if ($total_comments > 0)
		{
			$limit_sql = ($start == 0) ? $comments_per_page : $start .','. $comments_per_page;

			$sql = "SELECT c.*, u.user_id, u.username
					FROM ". ALBUM_COMMENT_TABLE ." AS c
						LEFT JOIN ". USERS_TABLE ." AS u ON c.comment_user_id = u.user_id
					WHERE c.comment_pic_id = '$pic_id'
					ORDER BY c.comment_id $sort_order
					LIMIT $limit_sql";

			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain comments information from the database', E_USER_WARNING);
			}

			$commentrow = array();

			while( $row = $db->sql_fetchrow($result) )
			{
				$commentrow[] = $row;
			}

			for ($i = 0; $i < count($commentrow); $i++)
			{
				if( ($commentrow[$i]['user_id'] == ALBUM_GUEST) or ($commentrow[$i]['username'] == '') )
				{
					$poster = ($commentrow[$i]['comment_username'] == '') ? '匿名用户' : $commentrow[$i]['comment_username'];
				}
				else
				{
					$poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $commentrow[$i]['user_id']) .'">'. $commentrow[$i]['username'] .'</a>';
				}

				$commentrow[$i]['comment_text'] = smilies_pass($commentrow[$i]['comment_text']);
				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('commentrow', array(
					'ID' => $commentrow[$i]['comment_id'],
					'ROW_CLASS' => $row_class,
					'POSTER' => $poster,
					'TIME' => create_date($board_config['default_dateformat'], $commentrow[$i]['comment_time'], $board_config['board_timezone']),
					'IP' => ($userdata['user_level'] == ADMIN) ? '<br/>IP地址: ' . decode_ip($commentrow[$i]['comment_user_ip']) : '',
					'TEXT' => nl2br($commentrow[$i]['comment_text']),
					'EDIT' => ( ( $auth_data['edit'] and ($commentrow[$i]['comment_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_edit_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.php?action=comment_edit&amp;comment_id=". $commentrow[$i]['comment_id']) .'">编辑</a>' : '',
					'DELETE' => ( ( $auth_data['delete'] and ($commentrow[$i]['comment_user_id'] == $userdata['user_id']) ) or ($auth_data['moderator'] and ($thiscat['cat_delete_level'] != ALBUM_ADMIN) ) or ($userdata['user_level'] == ADMIN) ) ? '<a href="'. append_sid("album.php?action=comment_delete&amp;comment_id=". $commentrow[$i]['comment_id']) .'">删除</a>' : ''
					)
				);
			}

			$template->assign_vars(array(
				'PAGINATION' => generate_pagination(append_sid("album.php?action=comment&amp;pic_id=$pic_id&amp;sort_order=$sort_order"), $total_comments, $comments_per_page, $start))
			);
		}

		$template->assign_block_vars('switch_comment', array());

		if (!$total_comments)
		{
			$template->assign_block_vars('switch_not_comment', array());
		}

		page_header('图片评论');

		$template->set_filenames(array(
			'body' => 'album/comment_body.tpl')
		);

		if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
		{
			$poster = ($thispic['pic_username'] == '') ? '匿名用户' : $thispic['pic_username'];
		}
		else
		{
			$poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
		}

		if ($auth_data['comment'] == 1)
		{
			$template->assign_block_vars('switch_comment_post', array());

			if( !$userdata['session_logged_in'] )
			{
				$template->assign_block_vars('switch_comment_post.logout', array());
			}
		}

		$template->assign_vars(array(
			'CAT_TITLE' => $thiscat['cat_title'],
			'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.php?action=cat&amp;cat_id=$cat_id") : append_sid("album.php?action=personal&amp;user_id=$user_id"),
			'U_THUMBNAIL' => append_sid("album.php?action=thumbnail&amp;pic_id=$pic_id"),
			'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.php?action=pic&amp;pic_id=$pic_id") : append_sid("album.php?action=page&amp;pic_id=$pic_id"),
			'PIC_TITLE' => $thispic['pic_title'],
			'PIC_DESC' => nl2br($thispic['pic_desc']),
			'POSTER' => $poster,
			'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
			'PIC_VIEW' => $thispic['pic_view_count'],
			'PIC_COMMENTS' => $total_comments,
			'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
			'S_MAX_LENGTH' => $album_config['desc_length'],
			'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
			'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : '',
			'S_ALBUM_ACTION' => append_sid("album.php?action=comment&amp;pic_id=$pic_id")
			)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		if ($auth_data['comment'] == 0)
		{
			if (!$userdata['session_logged_in'])
			{
				login_back("album.php&action=comment&pic_id=$pic_id");
			}
			else
			{
				trigger_error('没有权限评论', E_USER_ERROR);
			}
		}

		$comment_text = str_replace("\'", "''", htmlspecialchars(substr(trim($_POST['comment']), 0, $album_config['desc_length'])));
		$comment_username = (!$userdata['session_logged_in']) ? str_replace("\'", "''", substr(htmlspecialchars(trim($_POST['comment_username'])), 0, 32)) : str_replace("'", "''", htmlspecialchars(trim($userdata['username'])));

		if( empty($comment_text) )
		{
			trigger_error('描述不能为空', E_USER_ERROR);
		}

		if( ($thispic['pic_lock'] == 1) and (!$auth_data['moderator']) )
		{
			trigger_error('图片已锁定', E_USER_ERROR);
		}

		if (!$userdata['session_logged_in'])
		{
			if ($comment_username != '')
			{
				$result = validate_username($comment_username);
				if ( $result['error'] )
				{
					trigger_error($result['error_msg']);
				}
			}
		}

		$comment_time = time();
		$comment_user_id = $userdata['user_id'];
		$comment_user_ip = $userdata['session_ip'];

		$sql = "SELECT MAX(comment_id) AS max
				FROM ". ALBUM_COMMENT_TABLE;

		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not found comment_id', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		$comment_id = $row['max'] + 1;

		$sql = "INSERT INTO ". ALBUM_COMMENT_TABLE ." (comment_id, comment_pic_id, comment_user_id, comment_username, comment_user_ip, comment_time, comment_text)
				VALUES ('$comment_id', '$pic_id', '$comment_user_id', '$comment_username', '$comment_user_ip', '$comment_time', '$comment_text')";
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not insert new entry', E_USER_WARNING);
		}

		$message = '评论成功！<br />点击 <a href="' . append_sid("album.php?action=comment&amp;comment_id=$comment_id") . '#' . $comment_id . '">这里</a> 查看评论<br />点击 <a href="' . append_sid("album.php") . '">这里</a> 返回相册首页';

		trigger_error($message);
	}

	} elseif ( $action == 'comment_delete' ) {

	if( $album_config['comment'] == 0 )
	{
		trigger_error('没有权限', E_USER_ERROR);
	}

	if( isset($_GET['comment_id']) )
	{
		$comment_id = intval($_GET['comment_id']);
	}
	else if( isset($_POST['comment_id']) )
	{
		$comment_id = intval($_POST['comment_id']);
	}
	else
	{
		trigger_error('请指定comment_id');
	}

	$sql = "SELECT *
			FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_id = '$comment_id'";

	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query this comment information', E_USER_WARNING);
	}

	$thiscomment = $db->sql_fetchrow($result);

	if( empty($thiscomment) )
	{
		trigger_error('This comment does not exist');
	}

	$sql = "SELECT comment_id, comment_pic_id
			FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_id = '$comment_id'";

	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query comment and pic information', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	if( empty($row) )
	{
		trigger_error('评论不存在');
	}

	$pic_id = $row['comment_pic_id'];

	$sql = "SELECT p.*, u.user_id, u.username, COUNT(c.comment_id) as comments_count
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
			WHERE pic_id = '$pic_id'
			GROUP BY p.pic_id
			LIMIT 1";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$total_comments = $thispic['comments_count'];
	$comments_per_page = $board_config['posts_per_page'];

	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) )
	{
		trigger_error('图片不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($thispic['pic_cat_id'], $thiscat, 0, 0, 0, 1, 0, 1);

	if( ($album_user_access['comment'] == 0) or ($album_user_access['delete'] == 0) )
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=comment_delete&comment_id=$comment_id");
		}
		else
		{
			trigger_error('无权限', E_USER_ERROR);
		}
	}
	else
	{	
		if( (!$album_user_access['moderator']) or ($userdata['user_level'] != ADMIN) )
		{
			if ($thiscomment['comment_user_id'] != $userdata['user_id'])
			{
				trigger_error('无权限', E_USER_ERROR);
			}
		}
	}

	if( !isset($_POST['confirm']) )
	{
		if( isset($_POST['cancel']) )
		{
			redirect(append_sid("album.php?action=comment&comment_id=$comment_id"));
			exit;
		}

		page_header('删除');

		$template->set_filenames(array(
			'body' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' => '确认',

			'MESSAGE_TEXT' => '请确认是否删除？',

			'L_NO' => '否',
			'L_YES' => '是',

			'S_CONFIRM_ACTION' => append_sid("album.php?action=comment_delete&amp;comment_id=$comment_id"),
			)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		$sql = "DELETE
				FROM ". ALBUM_COMMENT_TABLE ."
				WHERE comment_id = '$comment_id'";

		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not delete this comment', E_USER_WARNING);
		}

		$message = '删除成功';

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= "<br />点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面";
		}
		else
		{
			$message .= "<br />点击 <a href=\"" . append_sid("album.php?action=personal&amp;user_id=$user_id") . "\">这里</a> 返回用户相册页面";
		}

		$message .= "<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

		trigger_error($message);
	}

	} elseif ( $action == 'comment_edit' ) {

	if( $album_config['comment'] == 0 )
	{
		trigger_error('您没有权限', E_USER_ERROR);
	}

	if( isset($_GET['comment_id']) )
	{
		$comment_id = intval($_GET['comment_id']);
	}
	else if( isset($_POST['comment_id']) )
	{
		$comment_id = intval($_POST['comment_id']);
	}
	else
	{
		trigger_error('No comment_id specified', E_USER_ERROR);
	}

	$sql = "SELECT *
			FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_id = '$comment_id'";

	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query this comment information', E_USER_WARNING);
	}

	$thiscomment = $db->sql_fetchrow($result);

	if( empty($thiscomment) )
	{
		trigger_error('This comment does not exist', E_USER_ERROR);
	}

	$sql = "SELECT comment_id, comment_pic_id
			FROM ". ALBUM_COMMENT_TABLE ."
			WHERE comment_id = '$comment_id'";

	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query comment and pic information', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	$pic_id = $row['comment_pic_id'];

	$sql = "SELECT p.*, u.user_id, u.username, COUNT(c.comment_id) as comments_count
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
			WHERE pic_id = '$pic_id'
			GROUP BY p.pic_id
			LIMIT 1";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$total_comments = $thispic['comments_count'];
	$comments_per_page = $board_config['posts_per_page'];

	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) )
	{
		trigger_error('图片不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($thispic['pic_cat_id'], $thiscat, 0, 0, 0, 1, 1, 0);

	if( ($album_user_access['comment'] == 0) or ($album_user_access['edit'] == 0) )
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=comment_edit&comment_id=$comment_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}
	else
	{	
		if( (!$album_user_access['moderator']) or ($userdata['user_level'] != ADMIN) )
		{
			if ($thiscomment['comment_user_id'] != $userdata['user_id'])
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}
		}
	}

	if( !isset($_POST['comment']) )
	{
		if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
		{
			$poster = ($thispic['pic_username'] == '') ? '匿名用户' : $thispic['pic_username'];
		}
		else
		{
			$poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
		}

		page_header('编辑评论');

		$template->set_filenames(array(
			'body' => 'album/comment_body.tpl')
		);

		$template->assign_block_vars('switch_comment_post', array());

		$template->assign_vars(array(
			'CAT_TITLE' => $thiscat['cat_title'],
			'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.php?action=cat&amp;cat_id=$cat_id") : append_sid("album.php?action=personal&amp;user_id=$user_id"),

			'U_THUMBNAIL' => append_sid("album.php?action=thumbnail&amp;pic_id=$pic_id"),
			'U_PIC' => append_sid("album.php?action=pic&amp;pic_id=$pic_id"),

			'PIC_TITLE' => $thispic['pic_title'],
			'PIC_DESC' => nl2br($thispic['pic_desc']),
			'POSTER' => $poster,
			'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
			'PIC_VIEW' => $thispic['pic_view_count'],
			'PIC_COMMENTS' => $total_comments,
			'S_MESSAGE' => $thiscomment['comment_text'],
			'S_MAX_LENGTH' => $album_config['desc_length'],
			'S_ALBUM_ACTION' => append_sid("album.php?action=comment_edit&amp;comment_id=$comment_id")
			)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		$comment_text = str_replace("\'", "''", htmlspecialchars(substr(trim($_POST['comment']), 0, $album_config['desc_length'])));

		if( empty($comment_text) )
		{
			trigger_error('评论不能为空', E_USER_ERROR);
		}

		$comment_edit_time = time();
		$comment_edit_user_id = $userdata['user_id'];

		$sql = "UPDATE ". ALBUM_COMMENT_TABLE ."
				SET comment_text = '$comment_text', comment_edit_time = '$comment_edit_time', comment_edit_count = comment_edit_count + 1, comment_edit_user_id = '$comment_edit_user_id'
				WHERE comment_id = '$comment_id'";

		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not update comment data', E_USER_WARNING);
		}

		$message = "评论修改成功<br />点击 <a href=\"" . append_sid("album.php?action=comment&amp;comment_id=$comment_id") . "#$comment_id\">这里</a> 返回评论列表<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

		trigger_error($message);
	}

}
elseif ( $action == 'delete' )
{

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		trigger_error('没有选中图片', E_USER_ERROR);
	}

	$sql = "SELECT *
			FROM ". ALBUM_TABLE ."
			WHERE pic_id = '$pic_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) )
	{
		trigger_error('图片不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 0, 0, 0, 0, 0, 1);

	if ($album_user_access['delete'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=delete&pic_id=$pic_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}
	else
	{
		if( (!$album_user_access['moderator']) and ($userdata['user_level'] != ADMIN) )
		{
			if ($thispic['pic_user_id'] != $userdata['user_id'])
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}
		}
	}

	if( !isset($_POST['confirm']) )
	{
		if( isset($_POST['cancel']) )
		{
			redirect(append_sid("album.php?action=cat&cat_id=$cat_id"));
			exit;
		}

		page_header('删除照片');

		$template->set_filenames(array(
			'body' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' => '确认',

			'MESSAGE_TEXT' => '请确认是否删除？',

			'L_NO' => '否',
			'L_YES' => '是',

			'S_CONFIRM_ACTION' => append_sid("album.php?action=delete&amp;pic_id=$pic_id"),
			)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		$sql = "DELETE FROM ". ALBUM_COMMENT_TABLE ."
				WHERE comment_pic_id = '$pic_id'";
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not delete related comments', E_USER_WARNING);
		}

		$sql = "DELETE FROM ". ALBUM_RATE_TABLE ."
				WHERE rate_pic_id = '$pic_id'";
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not delete related ratings', E_USER_WARNING);
		}

		if(($thispic['pic_thumbnail'] != '') and @file_exists(ALBUM_CACHE_PATH . $thispic['pic_thumbnail']))
		{
			@unlink(ALBUM_CACHE_PATH . $thispic['pic_thumbnail']);
		}

		@unlink(ALBUM_UPLOAD_PATH . $thispic['pic_filename']);

		$sql = "DELETE FROM ". ALBUM_TABLE ."
				WHERE pic_id = '$pic_id'";
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not delete DB entry', E_USER_WARNING);
		}

		$message = '图片删除成功';

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= "<br />点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面";
		}
		else
		{
			$message .= "<br />点击 <a href=\"" . append_sid("album.php?action=personal") . "\">这里</a> 返回用户相册页面";
		}

		$message .= "<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

		trigger_error($message);

	}

}
elseif ( $action == 'edit' )
{

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		trigger_error('请指定要编辑的图片', E_USER_ERROR);
	}

	$sql = "SELECT *
			FROM ". ALBUM_TABLE ."
			WHERE pic_id = '$pic_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) )
	{
		trigger_error('图片不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 0, 0, 0, 0, 1, 0);

	if ($album_user_access['edit'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=edit&pic_id=$pic_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}
	else
	{	
		if( (!$album_user_access['moderator']) and ($userdata['user_level'] != ADMIN) )
		{
			if ($thispic['pic_user_id'] != $userdata['user_id'])
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}
		}
	}

	if( !isset($_POST['pic_title']) )
	{
		page_header('编辑图片信息');

		$template->set_filenames(array(
			'body' => 'album/edit_body.tpl')
		);

		$template->assign_vars(array(
			'CAT_TITLE' => $thiscat['cat_title'],
			'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.php?action=cat&amp;cat_id=$cat_id") : append_sid("album.php?action=personal&amp;user_id=$user_id"),
			'PIC_TITLE' => $thispic['pic_title'],
			'PIC_DESC' => $thispic['pic_desc'],
			'S_PIC_DESC_MAX_LENGTH' => $album_config['desc_length'],
			'S_ALBUM_ACTION' => append_sid("album.php?action=edit&amp;pic_id=$pic_id"))
		);
		$template->pparse('body');

		page_footer();
	}
	else
	{
		$pic_title = str_replace("\'", "''", htmlspecialchars(trim($_POST['pic_title'])));
		$pic_desc = str_replace("\'", "''", htmlspecialchars(substr(trim($_POST['pic_desc']), 0, $album_config['desc_length'])));

		if( empty($pic_title) )
		{
			trigger_error('标题不能留空', E_USER_ERROR);
		}

		$sql = "UPDATE ". ALBUM_TABLE ."
				SET pic_title = '$pic_title', pic_desc= '$pic_desc'
				WHERE pic_id = '$pic_id'";
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not update pic information', E_USER_WARNING);
		}

		$message = '图片信息编辑成功';

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= '点击 <a href="' . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . '">这里</a> 返回相册分类页面';
		}
		else
		{
			$message .= '<br />点击 <a href="' . append_sid("album.php?action=personal") . '">这里</a> 返回用户相册页面';
		}

		$message .= '<br />点击<a href="' . append_sid("album.php") . '">这里</a> 返回相册首页';

		trigger_error($message);

	}

}
elseif ( $action == 'modcp' )
{

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else
	{
		$pic_id = FALSE;
	}

	if( $pic_id != FALSE )
	{
		$sql = "SELECT *
				FROM ". ALBUM_TABLE ."
				WHERE pic_id = '$pic_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query pic information', E_USER_WARNING);
		}
		$thispic = $db->sql_fetchrow($result);
		if( empty($thispic) )
		{
			trigger_error('图片不存在', E_USER_ERROR);
		}
		$cat_id = $thispic['pic_cat_id'];
		$user_id = $thispic['pic_user_id'];
	}
	else
	{
		if( isset($_POST['cat_id']) )
		{
			$cat_id = intval($_POST['cat_id']);
		}
		else if( isset($_GET['cat_id']) )
		{
			$cat_id = intval($_GET['cat_id']);
		}
		else
		{
			trigger_error('No categories specified', E_USER_ERROR);
		}
	}

	if( ($cat_id == PERSONAL_GALLERY) and (($_GET['mode'] == 'lock') or ($_GET['mode'] == 'unlock')) )
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}
	else
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$auth_data = album_user_access($cat_id, $thiscat, 0, 0, 0, 0, 0, 0);

	if( isset($_POST['mode']) )
	{
		if( isset($_POST['move']) )
		{
			$mode = 'move';
		}
		else if( isset($_POST['lock']) )
		{
			$mode = 'lock';
		}
		else if( isset($_POST['unlock']) )
		{
			$mode = 'unlock';
		}
		else if( isset($_POST['delete']) )
		{
			$mode = 'delete';
		}
		else if( isset($_POST['approval']) )
		{
			$mode = 'approval';
		}
		else if( isset($_POST['unapproval']) )
		{
			$mode = 'unapproval';
		}
		else
		{
			$mode = '';
		}
	}
	else if( isset($_GET['mode']) )
	{
		$mode = trim(htmlspecialchars($_GET['mode']));
	}
	else
	{
		$mode = '';
	}

	if ($auth_data['moderator'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=modcp&cat_id=$cat_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}

	if ($mode == '')
	{
		$pics_per_page = $board_config['topics_per_page'];
	if ( isset($_POST['start1']) )
	{
		$start1 = abs(intval($_POST['start1']));
		$start1 = ($start1 < 1) ? 1 : $start1;
		$start = (($start1 - 1) * $pics_per_page);
	}
	else
	{
		if( isset($_GET['start']) )
		{
			$start = intval($_GET['start']);
		}
		else if( isset($_POST['start']) )
		{
			$start = intval($_POST['start']);
		}
		else
		{
			$start = 0;
		}
		$start = ($start < 0) ? 0 : $start;
	}

		if( isset($_GET['sort_method']) )
		{
			switch ($_GET['sort_method'])
			{
				case 'pic_title':
					$sort_method = 'pic_title';
					break;
				case 'pic_user_id':
					$sort_method = 'pic_user_id';
					break;
				case 'pic_view_count':
					$sort_method = 'pic_view_count';
					break;
				case 'rating':
					$sort_method = 'rating';
					break;
				case 'comments':
					$sort_method = 'comments';
					break;
				case 'new_comment':
					$sort_method = 'new_comment';
					break;
				default:
					$sort_method = 'pic_time';
			}
		}
		else if( isset($_POST['sort_method']) )
		{
			switch ($_POST['sort_method'])
			{
				case 'pic_title':
					$sort_method = 'pic_title';
					break;
				case 'pic_user_id':
					$sort_method = 'pic_user_id';
					break;
				case 'pic_view_count':
					$sort_method = 'pic_view_count';
					break;
				case 'rating':
					$sort_method = 'rating';
					break;
				case 'comments':
					$sort_method = 'comments';
					break;
				case 'new_comment':
					$sort_method = 'new_comment';
					break;
				default:
					$sort_method = 'pic_time';
			}
		}
		else
		{
			$sort_method = 'pic_time';
		}

		if( isset($_GET['sort_order']) )
		{
			switch ($_GET['sort_order'])
			{
				case 'ASC':
					$sort_order = 'ASC';
					break;
				default:
					$sort_order = 'DESC';
			}
		}
		else if( isset($_POST['sort_order']) )
		{
			switch ($_POST['sort_order'])
			{
				case 'ASC':
					$sort_order = 'ASC';
					break;
				default:
					$sort_order = 'DESC';
			}
		}
		else
		{
			$sort_order = 'DESC';
		}

		$sql = "SELECT COUNT(pic_id) AS count
				FROM ". ALBUM_TABLE ."
				WHERE pic_cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not count pics in this category', E_USER_WARNING);
		}
		$row = $db->sql_fetchrow($result);

		$total_pics = $row['count'];

		if ($total_pics > 0)
		{
			$limit_sql = ($start == 0) ? $pics_per_page : $start .', '. $pics_per_page;

			$pic_approval_sql = '';
			if( ($userdata['user_level'] != ADMIN) and ($thiscat['cat_approval'] == ALBUM_ADMIN) )
			{
				$pic_approval_sql = 'AND p.pic_approval = 1';
			}

			$sql = "SELECT p.pic_id, p.pic_title, p.pic_user_id, p.pic_user_ip, p.pic_username, p.pic_time, p.pic_cat_id, p.pic_view_count, p.pic_lock, p.pic_approval, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(c.comment_id) AS comments, MAX(c.comment_id) AS new_comment
					FROM ". ALBUM_TABLE ." AS p
						LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
						LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
						LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
					WHERE p.pic_cat_id = '$cat_id' $pic_approval_sql
					GROUP BY p.pic_id
					ORDER BY $sort_method $sort_order
					LIMIT $limit_sql";
			if( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not query pics information', E_USER_WARNING);
			}

			$picrow = array();

			while( $row = $db->sql_fetchrow($result) )
			{
				$picrow[] = $row;
			}

			for ($i = 0; $i <count($picrow); $i++)
			{
				if( ($picrow[$i]['user_id'] == ALBUM_GUEST) or ($picrow[$i]['username'] == '') )
				{
					$pic_poster = ($picrow[$i]['pic_username'] == '') ? '匿名用户' : $picrow[$i]['pic_username'];
				}
				else
				{
					$pic_poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $picrow[$i]['user_id']) .'">'. $picrow[$i]['username'] .'</a>';
				}
				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('picrow', array(
					'PIC_ID' => $picrow[$i]['pic_id'],
					'ROW_CLASS' => $row_class,
					'PIC_TITLE' => '<a href="'. append_sid("album.php?action=pic&amp;pic_id=". $picrow[$i]['pic_id']) .'" target="_blank">'. $picrow[$i]['pic_title'] .'</a>',
					'POSTER' => $pic_poster,
					'TIME' => create_date($board_config['default_dateformat'], $picrow[$i]['pic_time'], $board_config['board_timezone']),
					'RATING' => ($picrow[$i]['rating'] == 0) ? '无评价' : round($picrow[$i]['rating'], 2),
					'COMMENTS' => $picrow[$i]['comments'],
					'LOCK' => ($picrow[$i]['pic_lock'] == 0) ? '' : '已锁定',
					'APPROVAL' => ($picrow[$i]['pic_approval'] == 0) ? '未审核' : '已审核'
					)
				);
			}

			$template->assign_vars(array(
				'PAGINATION' => generate_pagination(append_sid("album.php?action=modcp&amp;cat_id=$cat_id&amp;sort_method=$sort_method&amp;sort_order=$sort_order"), $total_pics, $pics_per_page, $start))
			);
		}
		else
		{
			$template->assign_block_vars('no_pics', array());
		}

		page_header('相册版主管理面板');

		$template->set_filenames(array(
			'body' => 'album/modcp_body.tpl')
		);

		$sort_rating_option = '';
		$sort_comments_option = '';
		if( $album_config['rate'] == 1 )
		{
			$sort_rating_option = '<option value="rating" ';
			$sort_rating_option .= ($sort_method == 'rating') ? 'selected="selected"' : '';
			$sort_rating_option .= '>评价</option>';
		}
		if( $album_config['comment'] == 1 )
		{
			$sort_comments_option = '<option value="comments" ';
			$sort_comments_option .= ($sort_method == 'comments') ? 'selected="selected"' : '';
			$sort_comments_option .= '>评论</option>';
			$sort_new_comment_option = '<option value="new_comment" ';
			$sort_new_comment_option .= ($sort_method == 'new_comment') ? 'selected="selected"' : '';
			$sort_new_comment_option .= '>新评论</option>';
		}

		$template->assign_vars(array(
			'U_VIEW_CAT' => append_sid("album.php?action=modcp&amp;cat_id=$cat_id"),
			'CAT_TITLE' => $thiscat['cat_title'],
			'S_ALBUM_ACTION' => append_sid("album.php?action=modcp&amp;cat_id=$cat_id"),
			'DELETE_BUTTON' => ($auth_data['delete'] == 1) ? '<input type="submit" name="delete" value="删除" />' : '',
			'APPROVAL_BUTTON' => ( ($userdata['user_level'] != ADMIN) and ($thiscat['cat_approval'] == ALBUM_ADMIN) ) ? '' : '<input type="submit" name="approval" value="通过" />',
			'UNAPPROVAL_BUTTON' => ( ($userdata['user_level'] != ADMIN) and ($thiscat['cat_approval'] == ALBUM_ADMIN) ) ? '' : '<input type="submit" name="unapproval" value="拒绝" />',
			'SORT_TIME' => ($sort_method == 'pic_time') ? 'selected="selected"' : '',
			'SORT_PIC_TITLE' => ($sort_method == 'pic_title') ? 'selected="selected"' : '',
			'SORT_USERNAME' => ($sort_method == 'pic_user_id') ? 'selected="selected"' : '',
			'SORT_VIEW' => ($sort_method == 'pic_view_count') ? 'selected="selected"' : '',
			'SORT_RATING_OPTION' => $sort_rating_option,
			'SORT_COMMENTS_OPTION' => $sort_comments_option,
			'SORT_NEW_COMMENT_OPTION' => $sort_new_comment_option,
			'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
			'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : ''
			)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		if ($mode == 'move')
		{
			if( !isset($_POST['target']) )
			{
				$pic_id_array = array();
				if ($pic_id != FALSE)
				{
					$pic_id_array[] = $pic_id;
				}
				else
				{
					if( isset($_POST['pic_id']) )
					{
						$pic_id_array = $_POST['pic_id'];
						if( !is_array($pic_id_array) )
						{
							trigger_error('请求错误', E_USER_ERROR);
						}
					}
					else
					{
						trigger_error('没有选中图片', E_USER_ERROR);
					}
				}
				for ($i = 0; $i < count($pic_id_array); $i++)
				{
					$template->assign_block_vars('pic_id_array', array(
						'VALUE' => $pic_id_array[$i])
					);
				}

				$sql = "SELECT *
						FROM ". ALBUM_CAT_TABLE ."
						WHERE cat_id <> '$cat_id'
						ORDER BY cat_order ASC";
				if( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not query categories list', E_USER_WARNING);
				}

				$catrows = array();

				while( $row = $db->sql_fetchrow($result) )
				{
					$album_user_access = album_user_access($row['cat_id'], $row, 0, 1, 0, 0, 0, 0);

					if ($album_user_access['upload'] == 1)
					{
						$catrows[] = $row;
					}
				}

				if( count($catrows) == 0 )
				{
					trigger_error('There is no more categories which you have permisson to move pics to', E_USER_ERROR);
				}

				$category_select = '<select name="target">';

				for ($i = 0; $i < count($catrows); $i++)
				{
					$category_select .= '<option value="'. $catrows[$i]['cat_id'] .'">'. $catrows[$i]['cat_title'] .'</option>';
				}

				$category_select .= '</select>';

				page_header('移动');

				$template->set_filenames(array(
					'body' => 'album/move_body.tpl')
				);

				$template->assign_vars(array(
					'S_ALBUM_ACTION' => append_sid("album.php?action=modcp&amp;mode=move&amp;cat_id=$cat_id"),
					'S_CATEGORY_SELECT' => $category_select)
				);

				$template->pparse('body');

				page_footer();
			}
			else
			{
				if( isset($_POST['pic_id']) )
				{
					$pic_id = $_POST['pic_id'];
					if( is_array($pic_id) )
					{
						$pic_id_sql = implode(',', $pic_id);
					}
					else
					{
						trigger_error('请求错误', E_USER_ERROR);
					}
				}
				else
				{
					trigger_error('没有选中图片', E_USER_ERROR);
				}

				$sql = "SELECT pic_id
						FROM ". ALBUM_TABLE ."
						WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not obtain album information', E_USER_WARNING);
				}
				if( $db->sql_numrows($result) > 0 )
				{
					trigger_error('图片不存在这个分类下', E_USER_ERROR);
				}

				$sql = "UPDATE ". ALBUM_TABLE ."
						SET pic_cat_id = ". intval($_POST['target']) ."
						WHERE pic_id IN ($pic_id_sql)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not update album information', E_USER_WARNING);
				}

				$message = "图片移动成功<br />点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("album.php?action=modcp&amp;cat_id=$cat_id") . "\">这里</a> 返回相册版主管理面板" . "<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

				trigger_error($message);
			}
		}
		else if ($mode == 'lock')
		{
			if ($pic_id != FALSE)
			{
				$pic_id_sql = $pic_id;
			}
			else
			{
				if( isset($_POST['pic_id']) )
				{
					$pic_id = $_POST['pic_id'];
					if( is_array($pic_id) )
					{
						$pic_id_sql = implode(',', $pic_id);
					}
					else
					{
						trigger_error('请求错误', E_USER_ERROR);
					}
				}
				else
				{
					trigger_error('没有指定图片', E_USER_ERROR);
				}
			}

			$sql = "SELECT pic_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain album information', E_USER_WARNING);
			}
			if( $db->sql_numrows($result) > 0 )
			{
				trigger_error("您没有权限", E_USER_ERROR);
			}

			$sql = "UPDATE ". ALBUM_TABLE ."
					SET pic_lock = 1
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not update album information', E_USER_WARNING);
			}

			$message = '图片已锁定<br />';

			if ($cat_id != PERSONAL_GALLERY)
			{
				$message .= "点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("album.php?action=modcp&amp;cat_id=$cat_id") . "\">这里</a> 返回相册版主管理面板";
			}
			else
			{
				$message .= "点击 <a href=\"" . append_sid("album.php?action=personal") . "\">这里</a> 返回用户相册页面";
			}

			$message .= "<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

			trigger_error($message);
		}
		else if ($mode == 'unlock')
		{
			if ($pic_id != FALSE)
			{
				$pic_id_sql = $pic_id;
			}
			else
			{
				if( isset($_POST['pic_id']) )
				{
					$pic_id = $_POST['pic_id'];
					if( is_array($pic_id) )
					{
						$pic_id_sql = implode(',', $pic_id);
					}
					else
					{
						trigger_error('请求错误', E_USER_ERROR);
					}
				}
				else
				{
					trigger_error('没有指定图片', E_USER_ERROR);
				}
			}

			$sql = "SELECT pic_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain album information', E_USER_WARNING);
			}
			if( $db->sql_numrows($result) > 0 )
			{
				trigger_error("您没有权限", E_USER_ERROR);
			}

			$sql = "UPDATE ". ALBUM_TABLE ."
					SET pic_lock = 0
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not update album information', E_USER_WARNING);
			}

			$message = '图片已解除锁定<br />';

			if ($cat_id != PERSONAL_GALLERY)
			{
				$message .= "点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("album.php?action=modcp&amp;cat_id=$cat_id") . "\">这里</a> 返回相册版主管理面板";
			}
			else
			{
				$message .= "点击 <a href=\"" . append_sid("album.php?action=personal") . "\">这里</a> 返回用户相册页面";
			}

			$message .= "<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

			trigger_error($message);
		}
		else if ($mode == 'approval')
		{
			if ($pic_id != FALSE)
			{
				$pic_id_sql = $pic_id;
			}
			else
			{
				if( isset($_POST['pic_id']) )
				{
					$pic_id = $_POST['pic_id'];
					if( is_array($pic_id) )
					{
						$pic_id_sql = implode(',', $pic_id);
					}
					else
					{
						trigger_error('请求错误', E_USER_ERROR);
					}
				}
				else
				{
					trigger_error('没有选中图片', E_USER_ERROR);
				}
			}

			$sql = "SELECT pic_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain album information', E_USER_WARNING);
			}
			if( $db->sql_numrows($result) > 0 )
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}

			$sql = "UPDATE ". ALBUM_TABLE ."
					SET pic_approval = 1
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not update album information', E_USER_WARNING);
			}

			$message = "该图片已通过审核<br />点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("album.php?action=modcp&amp;cat_id=$cat_id") . "\">这里</a> 返回相册版主管理面板<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

			trigger_error($message);
		}
		else if ($mode == 'unapproval')
		{
			if ($pic_id != FALSE)
			{
				$pic_id_sql = $pic_id;
			}
			else
			{
				if( isset($_POST['pic_id']) )
				{
					$pic_id = $_POST['pic_id'];
					if( is_array($pic_id) )
					{
						$pic_id_sql = implode(',', $pic_id);
					}
					else
					{
						trigger_error('请求错误', E_USER_ERROR);
					}
				}
				else
				{
					trigger_error('没有选中图片', E_USER_ERROR);
				}
			}

			$sql = "SELECT pic_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain album information', E_USER_WARNING);
			}
			if( $db->sql_numrows($result) > 0 )
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}

			$sql = "UPDATE ". ALBUM_TABLE ."
					SET pic_approval = 0
					WHERE pic_id IN ($pic_id_sql)";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not update album information', E_USER_WARNING);
			}

			$message = "已将图片设置为未审核<br />点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("album.php?action=modcp&amp;cat_id=$cat_id") . "\">这里</a> 返回相册版主管理面板<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

			trigger_error($message);
		}
		else if ($mode == 'delete')
		{
			if ($auth_data['delete'] == 0)
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}

			if( !isset($_POST['confirm']) )
			{
				$pic_id_array = array();
				if ($pic_id != FALSE)
				{
					$pic_id_array[] = $pic_id;
				}
				else
				{
					if( isset($_POST['pic_id']) )
					{
						$pic_id_array = $_POST['pic_id'];
						if( !is_array($pic_id_array) )
						{
							trigger_error('请求错误', E_USER_ERROR);
						}
					}
					else
					{
						trigger_error('没有选中图片', E_USER_ERROR);
					}
				}

				if ( isset($_POST['cancel']) )
				{
					$redirect = "album.php?action=modcp&cat_id=$cat_id";
					redirect(append_sid($redirect, true));
				}			

				$hidden_field = '';
				for ($i = 0; $i < count($pic_id_array); $i++)
				{
					$hidden_field .= '<input name="pic_id[]" type="hidden" value="'. $pic_id_array[$i] .'" />' . "\n";
				}

				page_header('删除图片确认');

				$template->set_filenames(array(
					'body' => 'confirm_body.tpl')
				);

				$template->assign_vars(array(
					'MESSAGE_TITLE' => '确认',
					'MESSAGE_TEXT' => '是否要删除？',
					'S_HIDDEN_FIELDS' => $hidden_field,
					'L_NO' => '否',
					'L_YES' => '是',
					'S_CONFIRM_ACTION' => append_sid("album.php?action=modcp&amp;mode=delete&amp;cat_id=$cat_id"),
					)
				);

				$template->pparse('body');

				page_footer();
			}
			else
			{
				if( isset($_POST['pic_id']) )
				{
					$pic_id = $_POST['pic_id'];
					if( is_array($pic_id) )
					{
						$pic_id_sql = implode(',', $pic_id);
					}
					else
					{
						trigger_error('请求错误', E_USER_ERROR);
					}
				}
				else
				{
					trigger_error('没有选中图片', E_USER_ERROR);
				}
				$sql = "SELECT pic_id
						FROM ". ALBUM_TABLE ."
						WHERE pic_id IN ($pic_id_sql) AND pic_cat_id <> $cat_id";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not obtain album information', E_USER_WARNING);
				}
				if( $db->sql_numrows($result) > 0 )
				{
					trigger_error('您没有权限', E_USER_ERROR);
				}

				$sql = "DELETE FROM ". ALBUM_COMMENT_TABLE ."
						WHERE comment_pic_id IN ($pic_id_sql)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not delete related comments', E_USER_WARNING);
				}

				$sql = "DELETE FROM ". ALBUM_RATE_TABLE ."
						WHERE rate_pic_id IN ($pic_id_sql)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not delete related ratings', E_USER_WARNING);
				}

				$sql = "SELECT pic_filename, pic_thumbnail
						FROM ". ALBUM_TABLE ."
						WHERE pic_id IN ($pic_id_sql)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not obtain filenames', E_USER_WARNING);
				}
				$filerow = array();
				while( $row = $db->sql_fetchrow($result) )
				{
					$filerow[] = $row;
				}
				for ($i = 0; $i < count($filerow); $i++)
				{
					if( ($filerow[$i]['pic_thumbnail'] != '') and (@file_exists(ALBUM_CACHE_PATH . $filerow[$i]['pic_thumbnail'])) )
					{
						@unlink(ALBUM_CACHE_PATH . $filerow[$i]['pic_thumbnail']);
					}
					@unlink(ALBUM_UPLOAD_PATH . $filerow[$i]['pic_filename']);
				}

				$sql = "DELETE FROM ". ALBUM_TABLE ."
						WHERE pic_id IN ($pic_id_sql)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not delete DB entry', E_USER_WARNING);
				}

				$message = "图片已删除<br />点击 <a href=\"" . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("album.php?action=modcp&amp;cat_id=$cat_id") . "\">这里</a> 返回相册版主管理面板<br />点击 <a href=\"" . append_sid("album.php") . "\">这里</a> 返回相册首页";

				trigger_error($message);
			}
		}
		else
		{
			trigger_error('Invalid_mode', E_USER_ERROR);
		}
	}

}
elseif ( $action == 'page' )
{

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		trigger_error('No pic_id set', E_USER_ERROR);
	}

	if( isset($_GET['mode']) ) 
	{ 
        if( ($_GET['mode'] == 'next') or ($_GET['mode'] == 'previous') ) 
        { 
        	$sql = "SELECT pic_id, pic_cat_id, pic_user_id 
           		FROM ". ALBUM_TABLE ." 
             	WHERE pic_id = $pic_id"; 

			if( !($result = $db->sql_query($sql)) ) 
			{ 
					trigger_error('Could not query pic information', E_USER_WARNING); 
			} 
	  
			$row = $db->sql_fetchrow($result); 
			$cur_pic_cat = $row['pic_cat_id'];

			if( empty($row) ) 
			{ 
					trigger_error('Bad pic_id', E_USER_ERROR); 
			} 

			$sql = "SELECT new.pic_id, new.pic_time 
							FROM ". ALBUM_TABLE ." AS new, ". ALBUM_TABLE ." AS cur 
							WHERE cur.pic_id = $pic_id 
									AND new.pic_id <> cur.pic_id 
									AND new.pic_cat_id = cur.pic_cat_id"; 
			$sql .= ($_GET['mode'] == 'next') ? " AND new.pic_time >= cur.pic_time" : " AND new.pic_time <= cur.pic_time"; 
			$sql .= ($row['pic_cat_id'] == PERSONAL_GALLERY) ? " AND new.pic_user_id = cur.pic_user_id" : ""; 
			$sql .= ($_GET['mode'] == 'next') ? " ORDER BY pic_time ASC LIMIT 1" : " ORDER BY pic_time DESC LIMIT 1"; 
			if( !($result = $db->sql_query($sql)) ) 
			{ 
					trigger_error('Could not query pic information', E_USER_WARNING); 
			} 

			$row = $db->sql_fetchrow($result); 

			$sql = "SELECT min(pic_id), max(pic_id)
				FROM ". ALBUM_TABLE ."
				WHERE pic_cat_id = $cur_pic_cat"; 

				if( !($result = $db->sql_query($sql)) ) 
				{ 
					trigger_error('Could not query pic information', E_USER_WARNING); 
				} 

			$next = $db->sql_fetchrow($result);
			
			$first_pic = $next['min(pic_id)'];
			$last_pic = $next['max(pic_id)'];
				
		if( empty($row) AND ($_GET['mode'] == 'next')) 
	    { 						  
			redirect(append_sid("album.php?action=page&pic_id=$first_pic"));
		} 
                if( empty($row) AND ($_GET['mode'] == 'previous')) 
                { 
                        redirect(append_sid("album.php?action=page&pic_id=$last_pic"));
                } 
						
                $pic_id = $row['pic_id'];
        } 
	}

	$sql = "SELECT p.*, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments
		FROM ". ALBUM_TABLE ." AS p
			LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
			LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
			LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
		WHERE pic_id = '$pic_id'
		GROUP BY p.pic_id";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	if( empty($thispic) or !file_exists(ALBUM_UPLOAD_PATH . $pic_filename) )
	{
		trigger_error('图片不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 1, 0, 0, 0, 0, 0);

	if ($album_user_access['view'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=page&pic_id=$pic_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}

	if ($userdata['user_level'] != ADMIN)
	{
		if( ($thiscat['cat_approval'] == ADMIN) or (($thiscat['cat_approval'] == MOD) and !$album_user_access['moderator']) )
		{
			if ($thispic['pic_approval'] != 1)
			{
				trigger_error('您没有权限', E_USER_ERROR);
			}
		}
	}

	page_header('PAGE');

	$template->set_filenames(array(
		'body' => 'album/page_body.tpl')
	);

	if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
	{
		$poster = ($thispic['pic_username'] == '') ? '匿名用户' : $thispic['pic_username'];
	}
	else
	{
		$poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
	}


	$template->assign_vars(array(
		'CAT_TITLE' => $thiscat['cat_title'],
		'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.php?action=cat&amp;cat_id=$cat_id") : append_sid("album.php?action=personal&amp;user_id=$user_id"),
		'U_PIC' => append_sid("album.php?action=pic&amp;pic_id=$pic_id"),
		'PIC_TITLE' => $thispic['pic_title'],
		'PIC_DESC' => nl2br($thispic['pic_desc']),
		'POSTER' => $poster,
		'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
		'PIC_VIEW' => $thispic['pic_view_count'],
		'PIC_RATING' => ($thispic['rating'] != 0) ? round($thispic['rating'], 2) : '无',
		'PIC_COMMENTS' => $thispic['comments'],
		'U_RATE' => append_sid("album.php?action=rate&amp;pic_id=$pic_id"),
		'U_COMMENT' => append_sid("album.php?action=comment&amp;pic_id=$pic_id"),
		'U_NEXT' => append_sid("album.php?action=page&amp;pic_id=$pic_id&amp;mode=next"),
		'U_PREVIOUS' => append_sid("album.php?action=page&amp;pic_id=$pic_id&amp;mode=previous"))
	);

	if ($album_config['rate'])
	{
		$template->assign_block_vars('rate_switch', array());
	}

	if ($album_config['comment'])
	{
		$template->assign_block_vars('comment_switch', array());
	}

	$template->pparse('body');

	page_footer();

}
elseif ( $action == 'personal' )
{

	if( isset($_POST['user_id']) )
	{
		$user_id = intval($_POST['user_id']);
	}
	else if( isset($_GET['user_id']) )
	{
		$user_id = intval($_GET['user_id']);
	}
	else
	{
		$user_id = $userdata['user_id'];
	}

	if( ($user_id < 1) and (!$userdata['session_logged_in']) )
	{
		login_back("album.php&action=personal");
	}

	$sql = "SELECT username
			FROM ". USERS_TABLE ."
			WHERE user_id = $user_id";

	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not get the username of this category owner', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	$username = $row['username'];

	if( empty($username) )
	{
		trigger_error('Sorry, this user does not exist', E_USER_ERROR);
	}

	$personal_gallery_access = personal_gallery_access(1,1);

	if( $personal_gallery_access['view'] == 0 )
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=personal&user_id=$user_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}

	if ($user_id == $userdata['user_id'])
	{
		if( $personal_gallery_access['upload'] == 0 )
		{
			trigger_error('对不起，个人相册已禁止上传', E_USER_ERROR);
		}
	}
	$pics_per_page = $album_config['rows_per_page'] * $album_config['cols_per_page'];
	if ( isset($_POST['start1']) )
	{
		$start1 = abs(intval($_POST['start1']));
		$start1 = ($start1 < 1) ? 1 : $start1;
		$start = (($start1 - 1) * $pics_per_page);
	}
	else
	{
	if( isset($_GET['start']) )
	{
		$start = intval($_GET['start']);
	}
	else if( isset($_POST['start']) )
	{
		$start = intval($_POST['start']);
	}
	else
	{
		$start = 0;
	}
	$start = ($start < 0) ? 0 : $start;
	}

	if( isset($_GET['sort_method']) )
	{
		switch ($_GET['sort_method'])
		{
			case 'pic_title':
				$sort_method = 'pic_title';
				break;
			case 'pic_view_count':
				$sort_method = 'pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = $album_config['sort_method'];
		}
	}
	else if( isset($_POST['sort_method']) )
	{
		switch ($_POST['sort_method'])
		{
			case 'pic_title':
				$sort_method = 'pic_title';
				break;
			case 'pic_view_count':
				$sort_method = 'pic_view_count';
				break;
			case 'rating':
				$sort_method = 'rating';
				break;
			case 'comments':
				$sort_method = 'comments';
				break;
			case 'new_comment':
				$sort_method = 'new_comment';
				break;
			default:
				$sort_method = $album_config['sort_method'];
		}
	}
	else
	{
		$sort_method = $album_config['sort_method'];
	}

	if( isset($_GET['sort_order']) )
	{
		switch ($_GET['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			case 'DESC':
				$sort_order = 'DESC';
				break;
			default:
				$sort_order = $album_config['sort_order'];
		}
	}
	else if( isset($_POST['sort_order']) )
	{
		switch ($_POST['sort_order'])
		{
			case 'ASC':
				$sort_order = 'ASC';
				break;
			case 'DESC':
				$sort_order = 'DESC';
				break;
			default:
				$sort_order = $album_config['sort_order'];
		}
	}
	else
	{
		$sort_order = $album_config['sort_order'];
	}

	$sql = "SELECT COUNT(pic_id) AS count
			FROM ". ALBUM_TABLE ."
			WHERE pic_cat_id = ". PERSONAL_GALLERY ."
				AND pic_user_id = $user_id";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not count pics', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	$total_pics = $row['count'];

	if ($total_pics > 0)
	{
		$limit_sql = ($start == 0) ? $pics_per_page : $start .','. $pics_per_page;

		$sql = "SELECT p.pic_id, p.pic_title, p.pic_desc, p.pic_user_id, p.pic_user_ip, p.pic_time, p.pic_view_count, p.pic_lock, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments, MAX(c.comment_id) as new_comment
				FROM ". ALBUM_TABLE ." AS p
					LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
					LEFT JOIN ". ALBUM_COMMENT_TABLE ." AS c ON p.pic_id = c.comment_pic_id
				WHERE p.pic_cat_id = ". PERSONAL_GALLERY ."
					AND p.pic_user_id = $user_id
				GROUP BY p.pic_id
				ORDER BY $sort_method $sort_order
				LIMIT $limit_sql";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query pics information', E_USER_WARNING);
		}

		$picrow = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			$picrow[] = $row;
		}

		for ($i = 0; $i < count($picrow); $i += $album_config['cols_per_page'])
		{

			for ($j = $i; $j < ($i + $album_config['cols_per_page']); $j++)
			{
				if( $j >= count($picrow) )
				{
					break;
				}

				if(!$picrow[$j]['rating'])
				{
					$picrow[$j]['rating'] = '0';
				}
				else
				{
					$picrow[$j]['rating'] = round($picrow[$j]['rating'], 2);
				}

				$row_class = ( !($j % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('picrow', array(
					'TITLE' => $picrow[$j]['pic_title'],
					'ROW_CLASS' => $row_class,
					'TIME' => create_date($board_config['default_dateformat'], $picrow[$j]['pic_time'], $board_config['board_timezone']),
					'VIEW' => $picrow[$j]['pic_view_count'],
					'RATING' => ($album_config['rate']) ? $picrow[$j]['rating'] : '',
					'U_RATING' => ($album_config['rate']) ? append_sid("album.php?action=rate&amp;pic_id=". $picrow[$j]['pic_id']) : '#',
					'COMMENTS' => ($album_config['comment']) ? $picrow[$j]['comments'] : '',
					'U_COMMENTS' => ($album_config['comment']) ? append_sid("album.php?action=comment&amp;pic_id=". $picrow[$j]['pic_id']) : '#',
					'EDIT' => ( ($userdata['user_level'] == ADMIN) or ($userdata['user_id'] == $picrow[$j]['pic_user_id']) ) ? '<a href="'. append_sid("album.php?action=edit&amp;pic_id=". $picrow[$j]['pic_id']) . '">编辑</a>|' : '',
					'DELETE' => ( ($userdata['user_level'] == ADMIN) or ($userdata['user_id'] == $picrow[$j]['pic_user_id']) ) ? '<a href="'. append_sid("album.php?action=delete&amp;pic_id=". $picrow[$j]['pic_id']) . '">删除</a>|' : '',
					'LOCK' => ($userdata['user_level'] == ADMIN) ? '<a href="'. append_sid("album.php?action=modcp&amp;mode=". (($picrow[$j]['pic_lock'] == 0) ? 'lock' : 'unlock') ."&amp;pic_id=". $picrow[$j]['pic_id']) .'">'. (($picrow[$j]['pic_lock'] == 0) ? '锁定' : '解锁') .'</a>' : '',
					'IP' => ($userdata['user_level'] == ADMIN) ? decode_ip($picrow[$j]['pic_user_ip']) : ''
					)
				);

				$template->assign_block_vars('picrow.piccol', array(
					'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.php?action=pic&amp;pic_id=". $picrow[$j]['pic_id']) : append_sid("album.php?action=page&amp;pic_id=". $picrow[$j]['pic_id']),
					'THUMBNAIL' => append_sid("album.php?action=thumbnail&amp;pic_id=". $picrow[$j]['pic_id']),
					'DESC' => $picrow[$j]['pic_desc']
					)
				);
			}
		}

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination(append_sid("album.php?action=personal&amp;user_id=$user_id&amp;sort_method=$sort_method&amp;sort_order=$sort_order"), $total_pics, $pics_per_page, $start))
		);
	}
	else
	{
		$template->assign_block_vars('no_pics', array());
	}

	$sort_rating_option = '';
	$sort_comments_option = '';
	if( $album_config['rate'] == 1 )
	{
		$sort_rating_option = '<option value="rating" ';
		$sort_rating_option .= ($sort_method == 'rating') ? 'selected="selected"' : '';
		$sort_rating_option .= '>等级</option>';
	}
	if( $album_config['comment'] == 1 )
	{
		$sort_comments_option = '<option value="comments" ';
		$sort_comments_option .= ($sort_method == 'comments') ? 'selected="selected"' : '';
		$sort_comments_option .= '>评论</option>';

		$sort_new_comment_option = '<option value="new_comment" ';
		$sort_new_comment_option .= ($sort_method == 'new_comment') ? 'selected="selected"' : '';
		$sort_new_comment_option .= '>新评论</option>';
	}

	page_header('我的相册');

	$template->set_filenames(array(
		'body' => 'album/personal_body.tpl')
	);

	if( $user_id == $userdata['user_id'] )
	{
		$template->assign_block_vars('your_personal_gallery', array());
	}

	$template->assign_vars(array(
		'USERNAME' => $username,
		'U_UPLOAD_PIC' => append_sid("album.php?action=upload&amp;cat_id=". PERSONAL_GALLERY),
		'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
		'S_COLS' => $album_config['cols_per_page'],
		'S_COL_WIDTH' => (100/$album_config['cols_per_page']) . '%',
		'SORT_TIME' => ($sort_method == 'pic_time') ? 'selected="selected"' : '',
		'SORT_PIC_TITLE' => ($sort_method == 'pic_title') ? 'selected="selected"' : '',
		'SORT_VIEW' => ($sort_method == 'pic_view_count') ? 'selected="selected"' : '',
		'SORT_RATING_OPTION' => $sort_rating_option,
		'SORT_COMMENTS_OPTION' => $sort_comments_option,
		'SORT_NEW_COMMENT_OPTION' => $sort_new_comment_option,
		'SORT_ASC' => ($sort_order == 'ASC') ? 'selected="selected"' : '',
		'SORT_DESC' => ($sort_order == 'DESC') ? 'selected="selected"' : '')
	);

	$template->pparse('body');

	page_footer();

}
elseif ( $action == 'personal_index' )
{

	$start = get_pagination_start($board_config['topics_per_page']);

	if ( isset($_GET['mode']) || isset($_POST['mode']) )
	{
		$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
		$mode = htmlspecialchars($mode);
	}
	else
	{
		$mode = 'joined';
	}

	if(isset($_POST['order']))
	{
		$sort_order = ($_POST['order'] == 'ASC') ? 'ASC' : 'DESC';
	}
	else if(isset($_GET['order']))
	{
		$sort_order = ($_GET['order'] == 'ASC') ? 'ASC' : 'DESC';
	}
	else
	{
		$sort_order = 'ASC';
	}

	$mode_types_text = array('日期', '用户名', '图片', '最后图片');
	$mode_types = array('joindate', 'username', 'pics', 'last_pic');

	$select_sort_mode = '<select name="mode">';
	for($i = 0; $i < count($mode_types_text); $i++)
	{
		$selected = ( $mode == $mode_types[$i] ) ? ' selected="selected"' : '';
		$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
	}
	$select_sort_mode .= '</select>';

	$select_sort_order = '<select name="order">';
	if($sort_order == 'ASC')
	{
		$select_sort_order .= '<option value="ASC" selected="selected">从低到高</option><option value="DESC">从高到低</option>';
	}
	else
	{
		$select_sort_order .= '<option value="ASC">从低到高</option><option value="DESC" selected="selected">从高到低</option>';
	}
	$select_sort_order .= '</select>';

	page_header('用户相册');

	$template->set_filenames(array(
		'body' => 'album/personal_index_body.tpl')
	);

	$template->assign_vars(array(
		'S_MODE_SELECT' => $select_sort_mode,
		'S_ORDER_SELECT' => $select_sort_order,
		'S_MODE_ACTION' => append_sid("album.php?action=personal_index")
		)
	);


	switch( $mode )
	{
		case 'joined':
			$order_by = "user_regdate ASC LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'username':
			$order_by = "username $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'pics':
			$order_by = "pics $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'last_pic':
			$order_by = "last_pic $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		default:
			$order_by = "user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
	}

	$sql = "SELECT u.username, u.user_id, u.user_regdate, COUNT(p.pic_id) AS pics, MAX(p.pic_id) AS last_pic
			FROM ". USERS_TABLE ." AS u, ". ALBUM_TABLE ." as p
			WHERE u.user_id <> ". ANONYMOUS ."
				AND u.user_id = p.pic_user_id
				AND p.pic_cat_id = ". PERSONAL_GALLERY ."
			GROUP BY user_id
			ORDER BY $order_by";

	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query users', E_USER_WARNING);
	}

	$memberrow = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$memberrow[] = $row;
	}

	for ($i = 0; $i < count($memberrow); $i++)
	{
		$template->assign_block_vars('memberrow', array(
			'ROW_CLASS' => ( !($i % 2) ) ? 'row1' : 'row2',
			'USERNAME' => $memberrow[$i]['username'],
			'U_VIEWGALLERY' => append_sid("album.php?action=personal&amp;user_id=". $memberrow[$i]['user_id']),
			'JOINED' => create_date($userdata['user_dateformat'], $memberrow[$i]['user_regdate'], $board_config['board_timezone']),
			'PICS' => $memberrow[$i]['pics'])
		);
	}

	$sql = "SELECT COUNT(DISTINCT u.user_id) AS total
			FROM ". USERS_TABLE ." AS u, ". ALBUM_TABLE ." AS p
			WHERE u.user_id <> ". ANONYMOUS ."
				AND u.user_id = p.pic_user_id
				AND p.pic_cat_id = ". PERSONAL_GALLERY;

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting total galleries', E_USER_WARNING);
	}

	if ( $total = $db->sql_fetchrow($result) )
	{
		$total_galleries = $total['total'];

		$pagination = ( $total_galleries > $board_config['topics_per_page'] ) ? generate_pagination("album.php?action=personal_index&amp;mode=$mode&amp;order=$sort_order", $total_galleries, $board_config['topics_per_page'], $start) : '';
	}

	$template->assign_vars(array(
		'PAGINATION' => $pagination)
	);

	if ( $total_galleries == 0 )
	{
		$template->assign_block_vars('no_pics', array());
	}

	$template->pparse('body');

	page_footer();

}
elseif ( $action == 'pic' )
{

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		trigger_error('没有选中图片');
	}

	$sql = "SELECT *
			FROM ". ALBUM_TABLE ."
			WHERE pic_id = '$pic_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$pic_filetype = substr($thispic['pic_filename'], strlen($thispic['pic_filename']) - 4, 4);
	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) or !file_exists(ALBUM_UPLOAD_PATH . $pic_filename) )
	{
		trigger_error('图片不存在');
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在');
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 1, 0, 0, 0, 0, 0);
	if ($album_user_access['view'] == 0)
	{
		trigger_error('您没有权限');
	}

	if ($userdata['user_level'] != ADMIN)
	{
		if( ($thiscat['cat_approval'] == ADMIN) or (($thiscat['cat_approval'] == MOD) and !$album_user_access['moderator']) )
		{
			if ($thispic['pic_approval'] != 1)
			{
				trigger_error('您没有权限');
			}
		}
	}

	if( ($album_config['hotlink_prevent'] == 1) and (isset($HTTP_SERVER_VARS['HTTP_REFERER'])) )
	{
		$check_referer = explode('?', $HTTP_SERVER_VARS['HTTP_REFERER']);
		$check_referer = trim($check_referer[0]);

		$good_referers = array();

		if ($album_config['hotlink_allowed'] != '')
		{
			$good_referers = explode(',', $album_config['hotlink_allowed']);
		}

		$good_referers[] = $board_config['server_name'] . $board_config['script_path'];

		$errored = TRUE;

		for ($i = 0; $i < count($good_referers); $i++)
		{
			$good_referers[$i] = trim($good_referers[$i]);

			if( (strstr($check_referer, $good_referers[$i])) and ($good_referers[$i] != '') )
			{
				$errored = FALSE;
			}
		}

		if ($errored)
		{
			trigger_error('您没有权限');
		}
	}

	$sql = "UPDATE ". ALBUM_TABLE ."
			SET pic_view_count = pic_view_count + 1
			WHERE pic_id = '$pic_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not update pic information', E_USER_WARNING);
	}

	switch ( $pic_filetype )
	{
		case '.png':
			header('Content-type: image/png');
			break;
		case '.gif':
			header('Content-type: image/gif');
			break;
		case '.jpg':
			header('Content-type: image/jpeg');
			break;
		default:
			trigger_error('The filename data in the DB was corrupted');
	}

	readfile(ALBUM_UPLOAD_PATH  . $thispic['pic_filename']);

	exit;

	} elseif ( $action == 'rate' ) {

	if( $album_config['rate'] == 0 )
	{
		trigger_error('您没有权限', E_USER_ERROR);
	}

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		trigger_error('没有选中图片', E_USER_ERROR);
	}

	$sql = "SELECT p.*, u.user_id, u.username, r.rate_pic_id, AVG(r.rate_point) AS rating
			FROM ". ALBUM_TABLE ." AS p
				LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
				LEFT JOIN ". ALBUM_RATE_TABLE ." AS r ON p.pic_id = r.rate_pic_id
			WHERE pic_id = '$pic_id'
			GROUP BY p.pic_id";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) )
	{
		trigger_error('图片不存在', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 0, 0, 1, 0, 0, 0);

	if ($album_user_access['rate'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=rate&pic_id=$pic_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}

	if( $userdata['session_logged_in'] )
	{
		$sql = "SELECT *
				FROM ". ALBUM_RATE_TABLE ."
				WHERE rate_pic_id = '$pic_id'
					AND rate_user_id = '". $userdata['user_id'] ."'
				LIMIT 1";

		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not query rating information', E_USER_WARNING);
		}

		if ($db->sql_numrows($result) > 0)
		{
			$already_rated = TRUE;
		}
		else
		{
			$already_rated = FALSE;
		}
	}

	if( !isset($_POST['rate']) )
	{
		if (!$already_rated)
		{
			for ($i = 0; $i < $album_config['rate_scale']; $i++)
			{
				$template->assign_block_vars('rate_row', array(
					'POINT' => ($i + 1)
					)
				);
			}
		}

		page_header('图片评价');

		$template->set_filenames(array(
			'body' => 'album/rate_body.tpl')
		);

		if( ($thispic['pic_user_id'] == ALBUM_GUEST) or ($thispic['username'] == '') )
		{
			$poster = ($thispic['pic_username'] == '') ? '匿名用户' : $thispic['pic_username'];
		}
		else
		{
			$poster = '<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $thispic['user_id']) .'">'. $thispic['username'] .'</a>';
		}

		$template->assign_vars(array(
			'CAT_TITLE' => $thiscat['cat_title'],
			'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.php?action=cat&amp;cat_id=$cat_id") : append_sid("album.php?action=personal&amp;user_id=$user_id"),
			'U_THUMBNAIL' => append_sid("album.php?action=thumbnail&amp;pic_id=$pic_id"),
			'U_PIC' => ($album_config['fullpic_popup']) ? append_sid("album.php?action=pic&amp;pic_id=$pic_id") : append_sid("album.php?action=page&amp;pic_id=$pic_id"),
			'PIC_TITLE' => $thispic['pic_title'],
			'PIC_DESC' => nl2br($thispic['pic_desc']),
			'POSTER' => $poster,
			'PIC_TIME' => create_date($board_config['default_dateformat'], $thispic['pic_time'], $board_config['board_timezone']),
			'PIC_VIEW' => $thispic['pic_view_count'],
			'PIC_RATING' => ($thispic['rating'] != 0) ? round($thispic['rating'], 2) : '没有评价',
			'S_RATE_MSG' => ($already_rated) ? '已评价' : '分数',
			'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
			'S_ALBUM_ACTION' => append_sid("album.php?action=rate&amp;pic_id=$pic_id"),

			)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		$rate_point = intval($_POST['rate']);

		if( ($rate_point <= 0) or ($rate_point > $album_config['rate_scale']) )
		{
			trigger_error('请输入分数', E_USER_ERROR);
		}

		$rate_user_id = $userdata['user_id'];
		$rate_user_ip = $userdata['session_ip'];

		if ($already_rated)
		{
			trigger_error('您已经进行评价', E_USER_ERROR);
		}

		$sql = "INSERT INTO ". ALBUM_RATE_TABLE ." (rate_pic_id, rate_user_id, rate_user_ip, rate_point)
				VALUES ('$pic_id', '$rate_user_id', '$rate_user_ip', '$rate_point')";

		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not insert new rating', E_USER_WARNING);
		}

		$message = '成功评价';

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= '<br />点击 <a href="' . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . '">这里</a> 返回相册分类页面';
		}
		else
		{
			$message .= '<br />点击 <a href=' . append_sid("album.php?action=personal&amp;user_id=$user_id") . '">这里</a> 返回用户相册页面';
		}

		$message .= '<br />点击 <a href=' . append_sid("album.php") . '">这里</a> 返回相册首页';

		trigger_error($message);
	}

}
elseif ( $action == 'thumbnail' )
{

	if( isset($_GET['pic_id']) )
	{
		$pic_id = intval($_GET['pic_id']);
	}
	else if( isset($_POST['pic_id']) )
	{
		$pic_id = intval($_POST['pic_id']);
	}
	else
	{
		trigger_error('没有选中图片');
	}

	$sql = "SELECT *
			FROM ". ALBUM_TABLE ."
			WHERE pic_id = '$pic_id'";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query pic information', E_USER_WARNING);
	}
	$thispic = $db->sql_fetchrow($result);

	$cat_id = $thispic['pic_cat_id'];
	$user_id = $thispic['pic_user_id'];

	$pic_filetype = substr($thispic['pic_filename'], strlen($thispic['pic_filename']) - 4, 4);
	$pic_filename = $thispic['pic_filename'];
	$pic_thumbnail = $thispic['pic_thumbnail'];

	if( empty($thispic) or !file_exists(ALBUM_UPLOAD_PATH . $pic_filename) )
	{
		trigger_error('图片不存在');
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($user_id);
	}

	if (empty($thiscat))
	{
		trigger_error('分类不存在');
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 1, 0, 0, 0, 0, 0);

	if ($album_user_access['view'] == 0)
	{
		trigger_error('您没有权限');
	}

	if ($userdata['user_level'] != ADMIN)
	{
		if( ($thiscat['cat_approval'] == ADMIN) or (($thiscat['cat_approval'] == MOD) and !$album_user_access['moderator']) )
		{
			if ($thispic['pic_approval'] != 1)
			{
				trigger_error('您没有权限');
			}
		}
	}

	if( ($album_config['hotlink_prevent'] == 1) and (isset($HTTP_SERVER_VARS['HTTP_REFERER'])) )
	{
		$check_referer = explode('?', $HTTP_SERVER_VARS['HTTP_REFERER']);
		$check_referer = trim($check_referer[0]);

		$good_referers = array();

		if ($album_config['hotlink_allowed'] != '')
		{
			$good_referers = explode(',', $album_config['hotlink_allowed']);
		}

		$good_referers[] = $board_config['server_name'] . $board_config['script_path'];

		$errored = TRUE;

		for ($i = 0; $i < count($good_referers); $i++)
		{
			$good_referers[$i] = trim($good_referers[$i]);

			if( (strstr($check_referer, $good_referers[$i])) and ($good_referers[$i] != '') )
			{
				$errored = FALSE;
			}
		}

		if ($errored)
		{
			trigger_error('您没有权限');
		}
	}

	if( ($pic_filetype != '.jpg') and ($pic_filetype != '.png') and ($pic_filetype != '.gif') )
	{
		header('Content-type: image/jpeg');
		readfile($images['no_thumbnail']);
		exit;
	}
	else
	{
		if( ($album_config['thumbnail_cache'] == 1) and ($pic_thumbnail != '') and file_exists(ALBUM_CACHE_PATH . $pic_thumbnail) )
		{
			switch ($pic_filetype)
			{
			  case '.gif':
				case '.jpg':
					header('Content-type: image/jpeg');
					break;
				case '.png':
					header('Content-type: image/png');
					break;
			}

			readfile(ALBUM_CACHE_PATH . $pic_thumbnail);
			exit;
		}

		$pic_size = @getimagesize(ALBUM_UPLOAD_PATH . $pic_filename);
		$pic_width = $pic_size[0];
		$pic_height = $pic_size[1];

		$gd_errored = FALSE;
		switch ($pic_filetype)
		{
		 case '.gif':
		  $read_function = 'imagecreatefromgif';
		  $pic_filetype = '.jpg';
	   break;
			case '.jpg':
				$read_function = 'imagecreatefromjpeg';
				break;
			case '.png':
				$read_function = 'imagecreatefrompng';
				break;
		}

		$src = @$read_function(ALBUM_UPLOAD_PATH  . $pic_filename);

		if (!$src)
		{
			$gd_errored = TRUE;
			$pic_thumbnail = '';
		}
		else if( ($pic_width > $album_config['thumbnail_size']) or ($pic_height > $album_config['thumbnail_size']) )
		{
			if ($pic_width > $pic_height)
			{
				$thumbnail_width = $album_config['thumbnail_size'];
				$thumbnail_height = $album_config['thumbnail_size'] * ($pic_height/$pic_width);
			}
			else
			{
				$thumbnail_height = $album_config['thumbnail_size'];
				$thumbnail_width = $album_config['thumbnail_size'] * ($pic_width/$pic_height);
			}

			$thumbnail = ($album_config['gd_version'] == 1) ? @imagecreate($thumbnail_width, $thumbnail_height) : @imagecreatetruecolor($thumbnail_width, $thumbnail_height);

			$resize_function = ($album_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

			@$resize_function($thumbnail, $src, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $pic_width, $pic_height);
		}
		else
		{
			$thumbnail = $src;
		}

		if (!$gd_errored)
		{
			if ($album_config['thumbnail_cache'] == 1)
			{
				$pic_thumbnail = $pic_filename;

				switch ($pic_filetype)
				{
					case '.jpg':
						@imagejpeg($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail, $album_config['thumbnail_quality']);
						break;
					case '.png':
						@imagepng($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail);
						break;
				}

				@chmod(ALBUM_CACHE_PATH . $pic_thumbnail, 0777);
			}

			switch ($pic_filetype)
			{
				case '.jpg':
					@imagejpeg($thumbnail, '', $album_config['thumbnail_quality']);
					break;
				case '.png':
					@imagepng($thumbnail);
					break;
			}

			exit;
		}
		else
		{
			header('Content-type: image/jpeg');
			readfile('images/nothumbnail.jpg');
			exit;
		}
	}

}
elseif ( $action == 'upload' )
{

	if( isset($_POST['cat_id']) )
	{
		$cat_id = intval($_POST['cat_id']);
	}
	else if( isset($_GET['cat_id']) )
	{
		$cat_id = intval($_GET['cat_id']);
	}
	else
	{
		trigger_error('No categories specified', E_USER_ERROR);
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		$sql = "SELECT c.*, COUNT(p.pic_id) AS count
				FROM ". ALBUM_CAT_TABLE ." AS c
					LEFT JOIN ". ALBUM_TABLE ." AS p ON c.cat_id = p.pic_cat_id
				WHERE c.cat_id = '$cat_id'
				GROUP BY c.cat_id
				LIMIT 1";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query category information', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);
	}
	else
	{
		$thiscat = init_personal_gallery_cat($userdata['user_id']);
	}

	$current_pics = $thiscat['count'];

	if (empty($thiscat))
	{
		trigger_error('分类不存在', E_USER_ERROR);
	}

	$album_user_access = album_user_access($cat_id, $thiscat, 0, 1, 0, 0, 0, 0);

	if ($album_user_access['upload'] == 0)
	{
		if (!$userdata['session_logged_in'])
		{
			login_back("album.php&action=upload&cat_id=$cat_id");
		}
		else
		{
			trigger_error('您没有权限', E_USER_ERROR);
		}
	}

	if ($cat_id != PERSONAL_GALLERY)
	{
		if ($album_config['max_pics'] >= 0)
		{
			if( $current_pics >= $album_config['max_pics'] )
			{
				trigger_error('已达到图片上传数量限制', E_USER_ERROR);
			}
		}

		$check_user_limit = FALSE;

		if( ($userdata['user_level'] != ADMIN) and ($userdata['session_logged_in']) )
		{
			if ($album_user_access['moderator'])
			{
				if ($album_config['mod_pics_limit'] >= 0)
				{
					$check_user_limit = 'mod_pics_limit';
				}
			}
			else
			{
				if ($album_config['user_pics_limit'] >= 0)
				{
					$check_user_limit = 'user_pics_limit';
				}
			}
		}

		if ($check_user_limit != FALSE)
		{
			$sql = "SELECT COUNT(pic_id) AS count
					FROM ". ALBUM_TABLE ."
					WHERE pic_user_id = '". $userdata['user_id'] ."'
						AND pic_cat_id = '$cat_id'";
			if( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not count your pic', E_USER_WARNING);
			}
			$row = $db->sql_fetchrow($result);
			$own_pics = $row['count'];

			if( $own_pics >= $album_config[$check_user_limit] )
			{
				trigger_error('已达到图片上传数量限制', E_USER_ERROR);
			}
		}
	}
	else
	{
		if( ($current_pics >= $album_config['personal_gallery_limit']) and ($album_config['personal_gallery_limit'] >= 0) )
		{
			trigger_error('已达到图片上传数量限制', E_USER_ERROR);
		}
	}

	if( !isset($_POST['pic_title']) )
	{
		$sql = "SELECT *
				FROM " . ALBUM_CAT_TABLE ."
				ORDER BY cat_order ASC";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not query categories list', E_USER_WARNING);
		}

		$catrows = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			$thiscat_access = album_user_access($row['cat_id'], $row, 0, 1, 0, 0, 0, 0);

			if ($thiscat_access['upload'] == 1)
			{
				$catrows[] = $row;
			}
		}

		$select_cat = '<select name="cat_id">';

		if ($cat_id == PERSONAL_GALLERY)
		{
			$select_cat .= '<option value="$cat_id" selected="selected">';
			$select_cat .= $userdata['username'] . '的相册';
			$select_cat .= '</option>';
		}

		for ($i = 0; $i < count($catrows); $i++)
		{
			$select_cat .= '<option value="'. $catrows[$i]['cat_id'] .'" ';
			$select_cat .= ($cat_id == $catrows[$i]['cat_id']) ? 'selected="selected"' : '';
			$select_cat .= '>'. $catrows[$i]['cat_title'] .'</option>';
		}

		$select_cat .= '</select>';

		page_header('上传图片');

		$template->set_filenames(array(
			'body' => 'album/upload_body.tpl')
		);

		$template->assign_vars(array(
			'U_VIEW_CAT' => ($cat_id != PERSONAL_GALLERY) ? append_sid("album.php?action=cat&amp;cat_id=$cat_id") : append_sid("album.php?action=personal"),
			'CAT_TITLE' => $thiscat['cat_title'],

			'SELECT_CAT' => $select_cat,
			'S_MAX_FILESIZE' => $album_config['max_file_size'],

			'S_MAX_WIDTH' => $album_config['max_width'],
			'S_MAX_HEIGHT' => $album_config['max_height'],


			'S_JPG' => ($album_config['jpg_allowed'] == 1) ? '是' : '否',
			'S_PNG' => ($album_config['png_allowed'] == 1) ? '是' : '否',
			'S_GIF' => ($album_config['gif_allowed'] == 1) ? '是' : '否',

			'S_THUMBNAIL_SIZE' => $album_config['thumbnail_size'],

			'S_ALBUM_ACTION' => append_sid("album.php?action=upload&amp;cat_id=$cat_id"),
			)
		);

		if ($album_config['gd_version'] == 0)
		{
			$template->assign_block_vars('switch_manual_thumbnail', array());
		}

		$template->pparse('body');

		page_footer();
	}
	else
	{
		$pic_title = str_replace("\'", "''", htmlspecialchars(trim($_POST['pic_title'])));
		$pic_desc = str_replace("\'", "''", htmlspecialchars(substr(trim($_POST['pic_desc']), 0, $album_config['desc_length'])));
		$pic_username = (!$userdata['session_logged_in']) ? substr(str_replace("\'", "''", htmlspecialchars(trim($_POST['pic_username']))), 0, 32) : str_replace("'", "''", $userdata['username']);

		if( empty($pic_title) )
		{
			trigger_error('图片的标题不能为空', E_USER_ERROR);
		}

		if( !isset($_FILES['pic_file']) )
		{
			trigger_error('Bad Upload', E_USER_ERROR);
		}

		if (!$userdata['session_logged_in'])
		{
			if ($pic_username != '')
			{
				$result = validate_username($pic_username);
				if ( $result['error'] )
				{
					trigger_error($result['error_msg'], E_USER_ERROR);
				}
			}
		}	

		$filetype = $_FILES['pic_file']['type'];
		$filesize = $_FILES['pic_file']['size'];
		$filetmp = $_FILES['pic_file']['tmp_name'];

		if ($album_config['gd_version'] == 0)
		{
			$thumbtype = $_FILES['pic_thumbnail']['type'];
			$thumbsize = $_FILES['pic_thumbnail']['size'];
			$thumbtmp = $_FILES['pic_thumbnail']['tmp_name'];
		}

		$pic_time = time();
		$pic_user_id = $userdata['user_id'];
		$pic_user_ip = $userdata['session_ip'];

		if( ($filesize == 0) or ($filesize > $album_config['max_file_size']) )
		{
			@unlink($filetmp);
			trigger_error('上传的图片过大或者图片文件已损坏', E_USER_ERROR);
		}

		if ($album_config['gd_version'] == 0)
		{
			if( ($thumbsize == 0) or ($thumbsize > $album_config['max_file_size']) )
			{
				@unlink($filetmp);
				trigger_error('图片超过限制大小或图片已损坏', E_USER_ERROR);
			}
		}

		switch ($filetype)
		{
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/pjpeg':
				if ($album_config['jpg_allowed'] == 0)
				{
					@unlink($filetmp);
					trigger_error('不允许的图片格式', E_USER_ERROR);
				}
				$pic_filetype = '.jpg';
				break;

			case 'image/png':
			case 'image/x-png':
				if ($album_config['png_allowed'] == 0)
				{
					@unlink($filetmp);
					trigger_error('不允许的图片格式', E_USER_ERROR);
				}
				$pic_filetype = '.png';
				break;

			case 'image/gif':
				if ($album_config['gif_allowed'] == 0)
				{
					@unlink($filetmp);
					trigger_error('不允许的图片格式', E_USER_ERROR);
				}
				$pic_filetype = '.gif';
				break;
			default:
				@unlink($filetmp);
				trigger_error('不允许的图片格式', E_USER_ERROR);
		}

		if ($album_config['gd_version'] == 0)
		{
			if ($filetype != $thumbtype)
			{
				@unlink($filetmp);
				trigger_error('格式图像和其缩略图不匹配', E_USER_ERROR);
			}
		}

		srand((double)microtime()*1000000);

		do
		{
			$pic_filename = md5(uniqid(rand())) . $pic_filetype;
		}
		while( file_exists(ALBUM_UPLOAD_PATH . $pic_filename) );

		if ($album_config['gd_version'] == 0)
		{
			$pic_thumbnail = $pic_filename;
		}

		$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';

		if ( @$ini_val('open_basedir') != '' )
		{
			if ( @phpversion() < '4.0.3' )
			{
				@unlink($filetmp);
				trigger_error('open_basedir is set and your PHP version does not allow move_uploaded_file<br /><br />Please contact your server admin', E_USER_WARNING);
			}

			$move_file = 'move_uploaded_file';
		}
		else
		{
			$move_file = 'copy';
		}


		$move_file($filetmp, ALBUM_UPLOAD_PATH . $pic_filename);

		@chmod(ALBUM_UPLOAD_PATH . $pic_filename, 0777);

		if ($album_config['gd_version'] == 0)
		{
			$move_file($thumbtmp, ALBUM_CACHE_PATH . $pic_thumbnail);

			@chmod(ALBUM_CACHE_PATH . $pic_thumbnail, 0777);
		}

		$pic_size = getimagesize(ALBUM_UPLOAD_PATH . $pic_filename);

		$pic_width = $pic_size[0];
		$pic_height = $pic_size[1];

		if ( ($pic_width > $album_config['max_width']) or ($pic_height > $album_config['max_height']) )
		{
			@unlink(ALBUM_UPLOAD_PATH . $pic_filename);

			if ($album_config['gd_version'] == 0)
			{
				@unlink(ALBUM_CACHE_PATH . $pic_thumbnail);
			}
			@unlink($filetmp);
			trigger_error('图片的像素宽或像素高过大', E_USER_ERROR);
		}

		if ($album_config['gd_version'] == 0)
		{
			$thumb_size = getimagesize(ALBUM_CACHE_PATH . $pic_thumbnail);

			$thumb_width = $thumb_size[0];
			$thumb_height = $thumb_size[1];

			if ( ($thumb_width > $album_config['thumbnail_size']) or ($thumb_height > $album_config['thumbnail_size']) )
			{
				@unlink(ALBUM_UPLOAD_PATH . $pic_filename);

				@unlink(ALBUM_CACHE_PATH . $pic_thumbnail);

				@unlink($filetmp);
				trigger_error('上传的缩略图过大', E_USER_ERROR);
			}
		}

		if( ($album_config['thumbnail_cache'] == 1) and ($pic_filetype != '.gif') and ($album_config['gd_version'] > 0) )
		{
			$gd_errored = FALSE;

			switch ($pic_filetype)
			{
				case '.jpg':
					$read_function = 'imagecreatefromjpeg';
					break;
				case '.png':
					$read_function = 'imagecreatefrompng';
					break;
			}

			$src = @$read_function(ALBUM_UPLOAD_PATH  . $pic_filename);

			if (!$src)
			{
				$gd_errored = TRUE;
				$pic_thumbnail = '';
			}
			else if( ($pic_width > $album_config['thumbnail_size']) or ($pic_height > $album_config['thumbnail_size']) )
			{
				if ($pic_width > $pic_height)
				{
					$thumbnail_width = $album_config['thumbnail_size'];
					$thumbnail_height = $album_config['thumbnail_size'] * ($pic_height/$pic_width);
				}
				else
				{
					$thumbnail_height = $album_config['thumbnail_size'];
					$thumbnail_width = $album_config['thumbnail_size'] * ($pic_width/$pic_height);
				}

				$thumbnail = ($album_config['gd_version'] == 1) ? @imagecreate($thumbnail_width, $thumbnail_height) : @imagecreatetruecolor($thumbnail_width, $thumbnail_height);

				$resize_function = ($album_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

				@$resize_function($thumbnail, $src, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $pic_width, $pic_height);
			}
			else
			{
				$thumbnail = $src;
			}

			if (!$gd_errored)
			{
				$pic_thumbnail = $pic_filename;

				switch ($pic_filetype)
				{
					case '.jpg':
						@imagejpeg($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail, $album_config['thumbnail_quality']);
						break;
					case '.png':
						@imagepng($thumbnail, ALBUM_CACHE_PATH . $pic_thumbnail);
						break;
				}

				@chmod(ALBUM_CACHE_PATH . $pic_thumbnail, 0777);

			}

		}
		else if ($album_config['gd_version'] > 0)
		{
			$pic_thumbnail = '';
		}

		$pic_approval = ($thiscat['cat_approval'] == 0) ? 1 : 0;

		$sql = "INSERT INTO ". ALBUM_TABLE ." (pic_filename, pic_thumbnail, pic_title, pic_desc, pic_user_id, pic_user_ip, pic_username, pic_time, pic_cat_id, pic_approval)
				VALUES ('$pic_filename', '$pic_thumbnail', '$pic_title', '$pic_desc', '$pic_user_id', '$pic_user_ip', '$pic_username', '$pic_time', '$cat_id', '$pic_approval')";
		if( !$result = $db->sql_query($sql) )
		{
			@unlink($filetmp);
			trigger_error('Could not insert new entry', E_USER_WARNING);
		}

		if ($thiscat['cat_approval'] == 0)
		{
			$message = '上传成功';
		}
		else
		{
			$message = '上传成功，请耐心等待管理员的审核';
		}
		@unlink($filetmp);

		if ($cat_id != PERSONAL_GALLERY)
		{
			$message .= '<br />点击 <a href=' . append_sid("album.php?action=cat&amp;cat_id=$cat_id") . '">这里</a> 返回相册分类页面';
		}
		else
		{
			$message .= '<br />点击 <a href="' . append_sid('album.php?action=personal') . '">这里</a> 返回用户相册页面';
		}

		$message .= '<br />点击 <a href="' . append_sid('album.php') . '">这里</a> 返回相册首页';

		trigger_error($message);
	}

}
else
{

	$sql = "SELECT c.*, COUNT(p.pic_id) AS count
			FROM ". ALBUM_CAT_TABLE ." AS c
				LEFT JOIN ". ALBUM_TABLE ." AS p ON c.cat_id = p.pic_cat_id
			WHERE cat_id <> 0
			GROUP BY cat_id
			ORDER BY cat_order ASC";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query categories list', E_USER_WARNING);
	}

	$catrows = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$album_user_access = album_user_access($row['cat_id'], $row, 1, 0, 0, 0, 0, 0);
		if ($album_user_access['view'] == 1)
		{
			$catrows[] = $row;
		}
	}

	$allowed_cat = '';

	for ($i = 0; $i < count($catrows); $i++)
	{
		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
		$allowed_cat .= ($allowed_cat == '') ? $catrows[$i]['cat_id'] : ',' . $catrows[$i]['cat_id'];
		$l_moderators = '';
		$moderators_list = '';

		$grouprows= array();

		if( $catrows[$i]['cat_moderator_groups'] != '')
		{
			$sql = "SELECT group_id, group_name
					FROM " . GROUPS_TABLE . "
					WHERE group_single_user <> 1
						AND group_type <> ". GROUP_HIDDEN ."
						AND group_id IN (". $catrows[$i]['cat_moderator_groups'] .")
					ORDER BY group_name ASC";
			if ( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not obtain usergroups data', E_USER_WARNING);
			}

			while( $row = $db->sql_fetchrow($result) )
			{
				$grouprows[] = $row;
			}
		}

		if( count($grouprows) > 0 )
		{
			$l_moderators = '版主';

			for ($j = 0; $j < count($grouprows); $j++)
			{
				$group_link = '<a href="'. append_sid("groupcp.php?". POST_GROUPS_URL .'='. $grouprows[$j]['group_id']) .'">'. $grouprows[$j]['group_name'] .'</a>';

				$moderators_list .= ($moderators_list == '') ? $group_link : ', ' . $group_link;
			}
		}

		if ($catrows[$i]['count'] == 0)
		{
			$last_pic_info = '没有图片';
			$u_last_pic = '';
			$last_pic_title = '';
		}
		else
		{
			if(($catrows[$i]['cat_approval'] == ALBUM_ADMIN) or ($catrows[$i]['cat_approval'] == ALBUM_MOD))
			{
				$pic_approval_sql = 'AND p.pic_approval = 1';
			}
			else
			{
				$pic_approval_sql = '';
			}

			$sql = "SELECT p.pic_id, p.pic_title, p.pic_user_id, p.pic_username, p.pic_time, p.pic_cat_id, u.user_id, u.username
					FROM ". ALBUM_TABLE ." AS p	LEFT JOIN ". USERS_TABLE ." AS u ON p.pic_user_id = u.user_id
					WHERE p.pic_cat_id = '". $catrows[$i]['cat_id'] ."' $pic_approval_sql
					ORDER BY p.pic_time DESC
					LIMIT 1";
			if ( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not get last pic information', E_USER_WARNING);
			}
			$lastrow = $db->sql_fetchrow($result);

			$last_pic_info = create_date($board_config['default_dateformat'], $lastrow['pic_time'], $board_config['board_timezone']);

			$last_pic_info .= '<br />';

			if( ($lastrow['user_id'] == ALBUM_GUEST) or ($lastrow['username'] == '') )
			{
				$last_pic_info .= ($lastrow['pic_username'] == '') ? '匿名用户' : $lastrow['pic_username'];
			}
			else
			{
				$last_pic_info .= '上传者：<a href="'. append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL .'='. $lastrow['user_id']) .'">'. $lastrow['username'] .'</a>';
			}

			if( !isset($album_config['last_pic_title_length']) )
			{
				$album_config['last_pic_title_length'] = 25;
			}

			$lastrow['pic_title'] = $lastrow['pic_title'];

			if (strlen($lastrow['pic_title']) > $album_config['last_pic_title_length'])
			{
				$lastrow['pic_title'] = substr($lastrow['pic_title'], 0, $album_config['last_pic_title_length']) . '...';
			}

			$last_pic_info .= '<br />图片标题：<a href="';

			$last_pic_info .= ($album_config['fullpic_popup']) ? append_sid("album.php?action=pic&amp;pic_id=". $lastrow['pic_id']) .'" target="_blank">' : append_sid("album.php?action=page&amp;pic_id=". $lastrow['pic_id']) .'">' ;

			$last_pic_info .= $lastrow['pic_title'] .'</a>';
		}

		$template->assign_block_vars('catrow', array(
			'ROW_CLASS'		=> $row_class,
			'U_VIEW_CAT' => append_sid("album.php?action=cat&amp;cat_id=". $catrows[$i]['cat_id']),
			'CAT_TITLE' => $catrows[$i]['cat_title'],
			'CAT_DESC' => $catrows[$i]['cat_desc'],
			'L_MODERATORS' => $l_moderators,
			'MODERATORS' => $moderators_list,
			'PICS' => $catrows[$i]['count'],
			'LAST_PIC_INFO' => $last_pic_info)
		);
	}

	if ($allowed_cat == '')
	{
		$template->assign_block_vars('no_cats', array());
	}

	page_header('相册');

	$template->set_filenames(array(
		'body' => 'album/index_body.tpl')
	);

	$template->assign_vars(array(

		'U_YOUR_PERSONAL_GALLERY' => append_sid("album.php?action=personal&amp;user_id=". $userdata['user_id']),
		'U_USERS_PERSONAL_GALLERIES' => append_sid("album.php?action=personal_index"),

		'S_COLS' => $album_config['cols_per_page'],
		'S_COL_WIDTH' => (100/$album_config['cols_per_page']) . '%',
		'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '')
	);

	$template->pparse('body');

	page_footer();
}

?>