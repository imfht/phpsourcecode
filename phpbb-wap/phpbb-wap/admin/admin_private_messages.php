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

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['系统']['私人信息'] = $filename;
	
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
$no_page_header = true;
require('pagestart.php');


$mode = get_var('mode', '');

if ($mode == 'delete')
{	
	if (isset($_POST['delete_all']))
	{
		$sql = 'TRUNCATE TABLE ' . PRIVMSGS_TABLE;
		
		if ( !$db->sql_query($sql))
		{
			trigger_error('无法删除私人信息', E_USER_WARNING);
		}
		
		$sql = 'TRUNCATE TABLE ' . PRIVMSGS_TEXT_TABLE;
		
		if ( !$db->sql_query($sql))
		{
			trigger_error('无法删除私人信息', E_USER_WARNING);
		}
		
		$sql = 'UPDATE ' . USERS_TABLE . ' 
			SET user_new_privmsg = 0, user_unread_privmsg = 0, user_last_privmsg = ' . time();
			
		if ( !$db->sql_query($sql))
		{
			trigger_error('无法更新用户私人信息数量', E_USER_WARNING);
		}
		
		trigger_error('全站的私人信息已成功删除<br />点击 <a href="' . append_sid('admin_private_messages.php') . '">这里</a> 返回上级', E_USER_ERROR);
		
	}
	else
	{
		if ( isset($_POST['pm_id_list']) )
		{
			$pms = $_POST['pm_id_list'];
		}
		else 
		{
			trigger_error('您没有选择用户', E_USER_ERROR);
		}
		
		for($i = 0; $i < count($pms); $i++)
		{
			$pm_id = intval($pms[$i]);
			
			$sql = 'DELETE FROM ' . PRIVMSGS_TABLE . ' 
				WHERE privmsgs_id = ' . $pm_id;
				
			if ( !$db->sql_query($sql) )
			{
				trigger_error('无法删除私人信息', E_USER_WARNING);
			}	
			
			$sql = 'DELETE FROM ' . PRIVMSGS_TEXT_TABLE . ' 
				WHERE privmsgs_text_id = ' . $pm_id;
				
			if ( !$db->sql_query($sql) )
			{
				trigger_error('无法删除私人信息', E_USER_WARNING);
			}
		}
		
		trigger_error('选中的私人信息已成功删除<br />点击 <a href="' . append_sid('admin_private_messages.php') . '">这里</a> 返回上级', E_USER_ERROR);
	}
}
else
{
	if (isset($_GET['view']))
	{
		$pm_view_id = get_var('view', '');
		
		if (!$pm_view_id)
		{
			trigger_error('请指定要查看的私人信息', E_USER_ERROR);
		}
		
		$pm_view_id = abs(intval($pm_view_id));
			
		$sql = 'SELECT u.username AS username_1, u.user_id AS user_id_1, u2.username AS username_2, u2.user_id AS user_id_2, pm.privmsgs_type, pm.privmsgs_subject, pm.privmsgs_date, pm.privmsgs_ip, pmt.privmsgs_text
			FROM ' . PRIVMSGS_TABLE . ' pm, ' . PRIVMSGS_TEXT_TABLE . ' pmt, ' . USERS_TABLE . ' u, ' . USERS_TABLE . ' u2 
			WHERE u.user_id = pm.privmsgs_from_userid 
				AND u2.user_id = pm.privmsgs_to_userid
				AND pmt.privmsgs_text_id = ' . $pm_view_id . '
				AND pm.privmsgs_id = ' . $pm_view_id;
		
		if ( !($result = $db->sql_query($sql)))
		{
			trigger_error('Could not query private message information', E_USER_WARNING);
		}	
		
		if ($db->sql_numrows($result))
		{
			$pm = $db->sql_fetchrow($result);
			
			if ($pm['privmsgs_type'] == PRIVMSGS_READ_MAIL)
			{
				$privmsgs_type = '已读信息';
			}
			elseif ($pm['privmsgs_type'] == PRIVMSGS_NEW_MAIL)
			{
				$privmsgs_type = '新收信息';
			}
			elseif ($pm['privmsgs_type'] == PRIVMSGS_SENT_MAIL)
			{
				$privmsgs_type = '已发信息';
			}
			elseif ($pm['privmsgs_type'] == PRIVMSGS_SAVED_IN_MAIL)
			{
				$privmsgs_type = '草稿箱信息';
			}
			elseif ($pm['privmsgs_type'] == PRIVMSGS_SAVED_OUT_MAIL)
			{
				$privmsgs_type = '草稿箱信息';
			}
			elseif ($pm['privmsgs_type'] == PRIVMSGS_UNREAD_MAIL)
			{
				$privmsgs_type = '未读信息';
			}
			else
			{
				$privmsgs_type = '';
			}
			
			page_header();

			$template->set_filenames(array(
				'body' => 'admin/private_messages.tpl')
			);
			
			$template->assign_vars(array(
				'ID'		=> $pm_view_id,
				'FROM' 		=> $pm['username_1'],
				'TO' 		=> $pm['username_2'],
				'DATE' 		=> create_date($board_config['default_dateformat'], $pm['privmsgs_date'], $board_config['board_timezone']),
				'IP' 		=> decode_ip($pm['privmsgs_ip']),
				'SUBJECT' 	=> $pm['privmsgs_subject'],
				'TYPE' 		=> $privmsgs_type,
				'MESSAGE' 	=> $pm['privmsgs_text'],
				'U_PM_LIST'	=> append_sid('admin_private_messages.php'),
				'S_DELETE_ACTION'	=> append_sid('admin_private_messages.php?mode=delete'))
			);
			
			$template->pparse('body');
			page_footer();
			
		}
		else
		{
			trigger_error('请查看的信息不存在', E_USER_ERROR);
		}
	}
	else
	{
		$start = get_pagination_start($board_config['topics_per_page']);

		// 得出当前页面的信息列表
		$sql = 'SELECT privmsgs_id, privmsgs_subject 
			FROM ' . PRIVMSGS_TABLE . '
			ORDER BY privmsgs_date DESC 
			LIMIT ' . $start . ', ' . $board_config['posts_per_page'];
		if ( !($result = $db->sql_query($sql)))
		{
			trigger_error('Could not query private message information', E_USER_WARNING);
		}	
		
		if ($db->sql_numrows($result))
		{
			$template->assign_block_vars('show', array());
		}
		else
		{
			$template->assign_block_vars('hide', array());
		}
		
		
		$i = 0;
		while ($pm_text = $db->sql_fetchrow($result))
		{
			$number 	= $i + 1 + $start;
			$row_class 	= ( !($i % 2) ) ? 'row1' : 'row2';
			
			$template->assign_block_vars('pmrow', array(
				'PM_ID'		=> $pm_text['privmsgs_id'],
				'L_MUNBER'	=> $number,
				'ROW_CLASS'	=> $row_class,
				'SUBJECT' 	=> $pm_text['privmsgs_subject'],
				'U_VIEW_PM'	=> append_sid('admin_private_messages.php?view=' . $pm_text['privmsgs_id']))
			);
			
			$i++;
		}

		// 统计出所有的信息
		$sql = 'SELECT COUNT(privmsgs_id) as total 
			FROM ' . PRIVMSGS_TABLE;

		if ( !($result = $db->sql_query($sql)))
		{
			trigger_error('无法统计私人信息记录', E_USER_WARNING);
		}

		$pm_count = $db->sql_fetchrow($result);

		$pagination = generate_pagination('admin_private_messages.php?', $pm_count['total'], $board_config['posts_per_page'], $start);

		page_header();

		$template->set_filenames(array(
			'body' => 'admin/private_messages_list.tpl')
		);
		$template->assign_vars(array(
			'S_DELETE_ACTION'	=> append_sid('admin_private_messages.php?mode=delete'))
		);
		$template->assign_vars(array(
			'PAGINATION' 	=> $pagination
		));

		$template->pparse('body');
		
		page_footer();
	}
}
?>