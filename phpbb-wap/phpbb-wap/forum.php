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

//session
$userdata = $session->start($user_ip, PAGE_INDEX);
init_userprefs($userdata);

$viewcat = ( !empty($_GET[POST_CAT_URL]) ) ? $_GET[POST_CAT_URL] : -1;

if( (!$board_config['index_spisok'] && !$userdata['session_logged_in']) || ($userdata['session_logged_in'] && !$userdata['user_index_spisok']) )
{
	if ( $viewcat < 0 )
	{
		$viewcat = -2;
	}
}
$sql = 'SELECT * 
	FROM ' . FORUMS_TABLE . ' 
	ORDER BY cat_id, forum_order';
if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询论坛表数据', E_USER_WARNING);
}

$forum_data = array();

while( $row = $db->sql_fetchrow($result) )
{
	$forum_data[] = $row;
}

$total_forums = count($forum_data);

$is_auth_ary = array();
$is_auth_ary = auth(AUTH_ALL, AUTH_LIST_ALL, $userdata, $forum_data);

// 取得论坛的分类
$sql = 'SELECT cat_id, cat_title, cat_order, cat_icon
	FROM ' . CATEGORIES_TABLE . '
	ORDER BY cat_order';	
if( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法取得论坛分类数据', E_USER_WARNING);
}
$category_rows = array();
while ($row = $db->sql_fetchrow($result))
{
	$category_rows[] = $row;
}

$db->sql_freeresult($result);
$total_categories = count($category_rows);

$display_categories = array();

for ($i = 0; $i < $total_forums; $i++ )
{
	if ($is_auth_ary[$forum_data[$i]['forum_id']]['auth_view'])
	{
		$display_categories[$forum_data[$i]['cat_id']] = true;
	}
}

for($i = 0; $i < $total_categories; $i++)
{
	$cat_id = $category_rows[$i]['cat_id'];

	if (isset($display_categories[$cat_id]) && $display_categories[$cat_id])
	{
		if ($category_rows[$i]['cat_icon'] != '')
		{
			$c_icon = '&nbsp;<img src="' . $category_rows[$i]['cat_icon'] . '"/>&nbsp;';
		}
		else 
		{
			$c_icon = '';
		}
		$template->assign_block_vars('catrow', array(
			'CAT_DESC' 		=> $category_rows[$i]['cat_title'],
			'CAT_ICON' 		=> $c_icon,
			'U_VIEWCAT' 	=> append_sid('forum.php?' . POST_CAT_URL . '=' . $cat_id))
		);
		
		if ( $viewcat == $cat_id || $viewcat == -1 )
		{
			for($j = 0; $j < $total_forums; $j++)
			{
				if ( $forum_data[$j]['cat_id'] == $cat_id )
				{
					$forum_id = $forum_data[$j]['forum_id'];

					if ( $is_auth_ary[$forum_id]['auth_view'] )
					{
						
						if ($forum_data[$j]['forum_icon'] != '')
						{
							$f_icon = '&nbsp;<img src="'.$forum_data[$j]['forum_icon'].'">&nbsp;';
						}
						else 
						{
							$f_icon = '&nbsp;' . make_style_image('forum_icon') . '&nbsp;';	
						}
						$template->assign_block_vars('catrow.forumrow',	array(
							'FORUM_ICON' 	=> $f_icon,
							'FORUM_NAME' 	=> $forum_data[$j]['forum_name'],
							'POSTS' 		=> $forum_data[$j]['forum_posts'],
							'TOPICS' 		=> $forum_data[$j]['forum_topics'],
							'U_VIEWFORUM' 	=> append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id))
						);
					}
				}
			}
		}
	}
}

$page_title = '论坛列表';

page_header($page_title);

$template->set_filenames(array(
	'body' => 'forum_body.tpl')
);

$template->pparse('body');

page_footer();
?>