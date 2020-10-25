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
	exit;
}

function process_quota_settings($mode, $id, $quota_type, $quota_limit_id = 0)
{
	global $db;

	$id = (int) $id;
	$quota_type = (int) $quota_type;
	$quota_limit_id = (int) $quota_limit_id;

	if ($mode == 'user')
	{
		if (!$quota_limit_id)
		{
			$sql = 'DELETE FROM ' . QUOTA_TABLE . "
				WHERE user_id = $id
					AND quota_type = $quota_type";
		}
		else
		{
			$sql = 'SELECT user_id 
				FROM ' . QUOTA_TABLE . " 
				WHERE user_id = $id
					AND quota_type = $quota_type";

			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not get Entry', E_USER_WARNING);
			}

			if ($db->sql_numrows($result) == 0)
			{
				$sql_ary = array(
					'user_id'		=> (int) $id,
					'group_id'		=> 0,
					'quota_type'	=> (int) $quota_type,
					'quota_limit_id'=> (int) $quota_limit_id
				);

				$sql = 'INSERT INTO ' . QUOTA_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			}
			else
			{
				$sql = 'UPDATE ' . QUOTA_TABLE . "
					SET quota_limit_id = $quota_limit_id
					WHERE user_id = $id
						AND quota_type = $quota_type";
			}
			$db->sql_freeresult($result);
		}
	
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Unable to update quota Settings', E_USER_WARNING);
		}
		
	}
	else if ($mode == 'group')
	{
		if (!$quota_limit_id)
		{
			$sql = 'DELETE FROM ' . QUOTA_TABLE . " 
				WHERE group_id = $id 
					AND quota_type = $quota_type";

			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Unable to delete quota Settings', E_USER_WARNING);
			}
		}
		else
		{
			$sql = 'SELECT group_id 
				FROM ' . QUOTA_TABLE . " 
				WHERE group_id = $id 
					AND quota_type = $quota_type";

			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not get Entry', E_USER_WARNING);
			}

			if ($db->sql_numrows($result) == 0)
			{
				$sql = 'INSERT INTO ' . QUOTA_TABLE . " (user_id, group_id, quota_type, quota_limit_id) 
					VALUES (0, $id, $quota_type, $quota_limit_id)";
			}
			else
			{
				$sql = 'UPDATE ' . QUOTA_TABLE . " SET quota_limit_id = $quota_limit_id 
					WHERE group_id = $id AND quota_type = $quota_type";
			}
	
			if (!$db->sql_query($sql))
			{
				trigger_error('Unable to update quota Settings', E_USER_WARNING);
			}
		}
	}
}

/*
* 对数组中的字符进行排序
* @参数 数组 需要排序的数组
* @参数 字符串 数组的键名，对键名的值进行排序
* @参数 字符串 排序的顺序
* @参数 ？？
* 返回排序后的数组
*/
function sort_multi_array ($sort_array, $key, $sort_order, $pre_string_sort = 0) 
{
	$last_element = count($sort_array) - 1;

	if (!$pre_string_sort)
	{
		$string_sort = (!is_numeric($sort_array[$last_element-1][$key]) ) ? true : false;
	}
	else
	{
		$string_sort = $pre_string_sort;
	}

	for ($i = 0; $i < $last_element; $i++) 
	{
		$num_iterations = $last_element - $i;

		for ($j = 0; $j < $num_iterations; $j++) 
		{
			$next = 0;
			$switch = false;
			if (!$string_sort)
			{
				if (($sort_order == 'DESC' && intval($sort_array[$j][$key]) < intval($sort_array[$j + 1][$key])) || ($sort_order == 'ASC' && intval($sort_array[$j][$key]) > intval($sort_array[$j + 1][$key])))
				{
					$switch = true;
				}
			}
			else
			{
				if (($sort_order == 'DESC' && strcasecmp($sort_array[$j][$key], $sort_array[$j + 1][$key]) < 0) || ($sort_order == 'ASC' && strcasecmp($sort_array[$j][$key], $sort_array[$j + 1][$key]) > 0))
				{
					$switch = true;
				}
			}

			if ($switch)
			{
				$temp = $sort_array[$j];
				$sort_array[$j] = $sort_array[$j + 1];
				$sort_array[$j + 1] = $temp;
			}
		}
	}

	return $sort_array;
}

function entry_exists($attach_id)
{
	global $db;

	$attach_id = (int) $attach_id;

	if (!$attach_id)
	{
		return false;
	}
	
	$sql = 'SELECT post_id, privmsgs_id
		FROM ' . ATTACHMENTS_TABLE . "
		WHERE attach_id = $attach_id";
	$result = $db->sql_query($sql);

	if (!$result)
	{
		trigger_error('Could not get Entry', E_USER_WARNING);
	}

	$ids = $db->sql_fetchrowset($result);
	$num_ids = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	$exists = false;
	
	for ($i = 0; $i < $num_ids; $i++)
	{
		if (intval($ids[$i]['post_id']) != 0)
		{
			$sql = 'SELECT post_id
				FROM ' . POSTS_TABLE . '
				WHERE post_id = ' . intval($ids[$i]['post_id']);
		}
		else if (intval($ids[$i]['privmsgs_id']) != 0)
		{
			$sql = 'SELECT privmsgs_id
				FROM ' . PRIVMSGS_TABLE . '
				WHERE privmsgs_id = ' . intval($ids[$i]['privmsgs_id']);
		}
		$result = $db->sql_query($sql);

		if (!$result)
		{
			trigger_error('Could not get Entry', E_USER_WARNING);
		}
	
		$num_rows = $db->sql_numrows($result);
		$db->sql_freeresult($result);

		if ($num_rows > 0)
		{
			$exists = true;
			break;
		}
	}

	return $exists;
}

/*
* 收集文件
*/
function collect_attachments()
{
	global $upload_dir, $board_config;

	$file_attachments = array(); 
	if (!intval($board_config['allow_ftp_upload']))
	{
		if ($dir = @opendir($upload_dir))
		{
			while ($new_dir = @readdir($dir))
			{
				// 获取doenload文件夹中的所有文件夹
				if (is_dir($upload_dir . '/' . $new_dir) && $new_dir != '.' && $new_dir != '..')
				{
					// 打开日期文件夹
					if ($file_dir = @opendir($upload_dir . '/' . $new_dir))
					{
						// 读出日期文件夹的文件
						while ($file = @readdir($file_dir))
						{
							// 去掉一些文件
							if($file != 'index.php' && $file != 'index.htm' && $file != 'index.html' && $file != '.htaccess' && is_file($upload_dir . '/' . $new_dir . '/' . $file))
							{
								$file_attachments[] = trim($file);
							}
						}						
					}	
				}
			}
		
			closedir($dir);
		}
		else
		{
			trigger_error('由于安全模式限制！ 无法打开目录. 请开启“允许FTP上传”来解决这个错误. 另外一种原因可能是目录 ' . $upload_dir . ' 不存在.', E_USER_ERROR);
		}
	}
	else
	{
		$conn_id = attach_init_ftp();

		$file_listing = array();

		$file_listing = @ftp_rawlist($conn_id, '');

		if (!$file_listing)
		{
			trigger_error('Unable to get Raw File Listing. Please be sure the LIST command is enabled at your FTP Server.', E_USER_ERROR);
		}

		for ($i = 0; $i < count($file_listing); $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd') 
				{	
					$dirinfo[0] = 1;
				}
				$dirinfo[1] = $regs[2];
				$dirinfo[2] = $regs[3];
				$dirinfo[3] = $regs[4];
				$dirinfo[4] = $regs[5];
			}
			
			if ($dirinfo[0] != 1 && $dirinfo[4] != 'index.php' && $dirinfo[4] != '.htaccess')
			{
				$file_attachments[] = trim($dirinfo[4]);
			}
		}

		@ftp_quit($conn_id);
	}

	return $file_attachments;
}

function get_formatted_dirsize()
{
	global $board_config, $upload_dir;

	$upload_dir_size = 0;

	if (!intval($board_config['allow_ftp_upload']))
	{
		if ($dir = @opendir($upload_dir))
		{
			while ($new_dir = @readdir($dir))
			{
				// 获取doenload文件夹中的所有文件夹
				if (is_dir($upload_dir . '/' . $new_dir) && $new_dir != '.' && $new_dir != '..')
				{
					// 打开日期文件夹
					if ($file_dir = @opendir($upload_dir . '/' . $new_dir))
					{
						// 读出日期文件夹的文件
						while ($file = @readdir($file_dir))
						{
							// 去掉一些文件
							if($file != 'index.php' && $file != 'index.htm' && $file != 'index.html' && $file != '.htaccess' && is_file($upload_dir . '/' . $new_dir . '/' . $file))
							{
								$upload_dir_size += @filesize($upload_dir . '/' . $new_dir . '/' . $file);
							}
						}						
					}	
				}
			}
		
			@closedir($dir);
		}
		else
		{
			$upload_dir_size = '不可用';
			return $upload_dir_size;
		}
	}
	else
	{
		$conn_id = attach_init_ftp();

		$file_listing = array();

		$file_listing = @ftp_rawlist($conn_id, '');

		if (!$file_listing)
		{
			$upload_dir_size = '不可用';
			return $upload_dir_size;
		}

		for ($i = 0; $i < count($file_listing); $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd') 
				{	
					$dirinfo[0] = 1;
				}
				$dirinfo[1] = $regs[2];
				$dirinfo[2] = $regs[3]; 
				$dirinfo[3] = $regs[4]; 
				$dirinfo[4] = $regs[5]; 
			}
			
			if ($dirinfo[0] != 1 && $dirinfo[4] != 'index.php' && $dirinfo[4] != '.htaccess')
			{
				$upload_dir_size += $dirinfo[1];
			}
		}

		@ftp_quit($conn_id);
	}

	if ($upload_dir_size >= 1048576)
	{
		$upload_dir_size = round($upload_dir_size / 1048576 * 100) / 100 . ' MB';
	}
	else if ($upload_dir_size >= 1024)
	{
		$upload_dir_size = round($upload_dir_size / 1024 * 100) / 100 . ' KB';
	}
	else
	{
		$upload_dir_size = $upload_dir_size . ' Bytes';
	}

	return $upload_dir_size;
}

function search_attachments($order_by, &$total_rows)
{
	global $db, $_POST, $_GET;
	
	$where_sql = array();

	$search_vars = array('search_keyword_fname', 'search_keyword_comment', 'search_author', 'search_size_smaller', 'search_size_greater', 'search_count_smaller', 'search_count_greater', 'search_days_greater', 'search_forum', 'search_cat');
	
	for ($i = 0; $i < count($search_vars); $i++)
	{
		$$search_vars[$i] = get_var($search_vars[$i], '');
	}

	if ($search_author != '')
	{
		$search_author = addslashes(html_entity_decode($search_author));
		$search_author = stripslashes(phpbb_clean_username($search_author));
		$search_author = str_replace('*', '%', $db->sql_escape($search_author));

		$sql = 'SELECT user_id
			FROM ' . USERS_TABLE . "
			WHERE username LIKE '$search_author'";

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Couldn\'t obtain list of matching users (searching for: ' . $search_author . ')', E_USER_WARNING);
		}

		$matching_userids = '';
		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				$matching_userids .= (($matching_userids != '') ? ', ' : '') . intval($row['user_id']);
			}
			while ($row = $db->sql_fetchrow($result));
			
			$db->sql_freeresult($result);
		}
		else
		{
			trigger_error('没有附件符合你的搜索条件', E_USER_ERROR);
		}

		$where_sql[] = ' (t.user_id_1 IN (' . $matching_userids . ')) ';
	}

	if ($search_keyword_fname != '')
	{
		$match_word = str_replace('*', '%', $search_keyword_fname);
		$where_sql[] = " (a.real_filename LIKE '" . $db->sql_escape($match_word) . "') ";
	}

	if ($search_keyword_comment != '')
	{
		$match_word = str_replace('*', '%', $search_keyword_comment);
		$where_sql[] = " (a.comment LIKE '" . $db->sql_escape($match_word) . "') ";
	}

	if ($search_count_smaller != '' || $search_count_greater != '')
	{
		if ($search_count_smaller != '')
		{
			$where_sql[] = ' (a.download_count < ' . (int) $search_count_smaller . ') ';
		}
		else if ($search_count_greater != '')
		{
			$where_sql[] = ' (a.download_count > ' . (int) $search_count_greater . ') ';
		}
	}

	if ($search_size_smaller != '' || $search_size_greater != '')
	{
		if ($search_size_smaller != '')
		{
			$where_sql[] = ' (a.filesize < ' . (int) $search_size_smaller . ') ';
		}
		else if ($search_size_greater != '')
		{
			$where_sql[] = ' (a.filesize > ' . (int) $search_size_greater . ') ';
		}
	}

	if ($search_days_greater != '')
	{
		$where_sql[] = ' (a.filetime < ' . ( time() - ((int) $search_days_greater * 86400)) . ') ';
	}

	if ($search_forum)
	{
		$where_sql[] = ' (p.forum_id = ' . intval($search_forum) . ') ';
	}

	$sql = 'SELECT a.*, t.post_id, p.post_time, p.topic_id
		FROM ' . ATTACHMENTS_TABLE . ' t, ' . ATTACHMENTS_DESC_TABLE . ' a, ' . POSTS_TABLE . ' p WHERE ';
	
	if (count($where_sql) > 0)
	{
		$sql .= implode('AND', $where_sql) . ' AND ';
	}

	$sql .= 't.post_id = p.post_id AND a.attach_id = t.attach_id ';
	
	$total_rows_sql = $sql;

	$sql .= $order_by; 

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t query attachments', E_USER_WARNING);
	}

	$attachments = $db->sql_fetchrowset($result);
	$num_attach = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_attach == 0)
	{
		trigger_error('没有找到匹配的附件', E_USER_ERROR);
	}

	if (!($result = $db->sql_query($total_rows_sql)))
	{
		trigger_error('Could not query attachments', E_USER_WARNING);
	}

	$total_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	return $attachments;
}

function limit_array($array, $start, $pagelimit)
{
	$limit = (count($array) < ($start + $pagelimit)) ? count($array) : $start + $pagelimit;

	$limit_array = array();

	for ($i = $start; $i < $limit; $i++)
	{
		$limit_array[] = $array[$i];
	}

	return $limit_array;
}

?>