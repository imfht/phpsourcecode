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

function delete_attachment($post_id_array = 0, $attach_id_array = 0, $page = 0, $user_id = 0)
{
	global $db;

	if ($post_id_array === 0 && $attach_id_array === 0 && $page === 0)
	{
		return;
	}

	if ($post_id_array === 0 && $attach_id_array !== 0)
	{
		$post_id_array = array();

		if (!is_array($attach_id_array))
		{
			if (strstr($attach_id_array, ', '))
			{
				$attach_id_array = explode(', ', $attach_id_array);
			}
			else if (strstr($attach_id_array, ','))
			{
				$attach_id_array = explode(',', $attach_id_array);
			}
			else
			{
				$attach_id = intval($attach_id_array);
				$attach_id_array = array();
				$attach_id_array[] = $attach_id;
			}
		}

		$p_id = 'post_id';

		$sql = "SELECT $p_id 
			FROM " . ATTACHMENTS_TABLE . '
				WHERE attach_id IN (' . implode(', ', $attach_id_array) . ")
			GROUP BY $p_id";

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not select ids', E_USER_WARNING);
		}

		$num_post_list = $db->sql_numrows($result);

		if ($num_post_list == 0)
		{
			$db->sql_freeresult($result);
			return;
		}

		while ($row = $db->sql_fetchrow($result))
		{
			$post_id_array[] = intval($row[$p_id]);
		}
		$db->sql_freeresult($result);
	}
		
	if (!is_array($post_id_array))
	{
		if (trim($post_id_array) == '')
		{
			return;
		}

		if (strstr($post_id_array, ', '))
		{
			$post_id_array = explode(', ', $post_id_array);
		}
		else if (strstr($post_id_array, ','))
		{
			$post_id_array = explode(',', $post_id_array);
		}
		else
		{
			$post_id = intval($post_id_array);

			$post_id_array = array();
			$post_id_array[] = $post_id;
		}
	}
		
	if (!count($post_id_array))
	{
		return;
	}

	if ($attach_id_array === 0)
	{
		$attach_id_array = array();

		$whereclause = 'WHERE post_id IN (' . implode(', ', $post_id_array) . ')';
			
		$sql = 'SELECT attach_id 
			FROM ' . ATTACHMENTS_TABLE . " $whereclause 
			GROUP BY attach_id";

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not select Attachment Ids', E_USER_WARNING);
		}

		$num_attach_list = $db->sql_numrows($result);

		if ($num_attach_list == 0)
		{
			$db->sql_freeresult($result);
			return;
		}

		while ($row = $db->sql_fetchrow($result))
		{
			$attach_id_array[] = (int) $row['attach_id'];
		}
		$db->sql_freeresult($result);
	}
	
	if (!is_array($attach_id_array))
	{
		if (strstr($attach_id_array, ', '))
		{
			$attach_id_array = explode(', ', $attach_id_array);
		}
		else if (strstr($attach_id_array, ','))
		{
			$attach_id_array = explode(',', $attach_id_array);
		}
		else
		{
			$attach_id = intval($attach_id_array);

			$attach_id_array = array();
			$attach_id_array[] = $attach_id;
		}
	}

	if (!count($attach_id_array))
	{
		return;
	}

	$sql_id = 'post_id';

	if (count($post_id_array) && count($attach_id_array))
	{
		$sql = 'DELETE FROM ' . ATTACHMENTS_TABLE . ' 
			WHERE attach_id IN (' . implode(', ', $attach_id_array) . ") 
				AND $sql_id IN (" . implode(', ', $post_id_array) . ')';

		if ( !($db->sql_query($sql)) )   
		{
			trigger_error('无法删除附件', E_USER_WARNING);   
		} 
	
		for ($i = 0; $i < count($attach_id_array); $i++)
		{
			$sql = 'SELECT attach_id 
				FROM ' . ATTACHMENTS_TABLE . ' 
					WHERE attach_id = ' . (int) $attach_id_array[$i];
			
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not select Attachment Ids', E_USER_WARNING);
			}
			
			$num_rows = $db->sql_numrows($result);
			$db->sql_freeresult($result);

			if ($num_rows == 0)
			{
				$sql = 'SELECT attach_id, physical_filename, thumbnail
					FROM ' . ATTACHMENTS_DESC_TABLE . '
					WHERE attach_id = ' . (int) $attach_id_array[$i];
	
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Couldn\'t query attach description table', E_USER_WARNING);
				}
				
				$num_rows = $db->sql_numrows($result);

				if ($num_rows != 0)
				{
					$num_attach = $num_rows;
					$attachments = $db->sql_fetchrowset($result);
					$db->sql_freeresult($result);

					// delete attachments
					for ($j = 0; $j < $num_attach; $j++)
					{
						unlink_attach($attachments[$j]['physical_filename']);
	
						if (intval($attachments[$j]['thumbnail']) == 1)
						{
							unlink_attach($attachments[$j]['physical_filename'], MODE_THUMBNAIL);
						}
					
						$sql = 'DELETE FROM ' . ATTACHMENTS_DESC_TABLE . '
							WHERE attach_id = ' . (int) $attachments[$j]['attach_id'];

						if ( !($db->sql_query($sql)) )
						{
							trigger_error('无法删除附件', E_USER_WARNING);
						}
					}
				}
				else
				{
					$db->sql_freeresult($result);
				}
			}
		}
	}

	if (count($post_id_array))
	{
		$sql = 'SELECT topic_id 
			FROM ' . POSTS_TABLE . ' 
			WHERE post_id IN (' . implode(', ', $post_id_array) . ') 
			GROUP BY topic_id';
	
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Couldn\'t select Topic ID', E_USER_WARNING);
		}

		while ($row = $db->sql_fetchrow($result))
		{
			attachment_sync_topic($row['topic_id']);
		}
		$db->sql_freeresult($result);
	}
}

?>