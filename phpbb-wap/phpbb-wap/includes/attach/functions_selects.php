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

function group_select($select_name, $default_group = 0)
{
	global $db;

	$sql = 'SELECT group_id, group_name
		FROM ' . EXTENSION_GROUPS_TABLE . '
		ORDER BY group_name';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error("Couldn't query Extension Groups Table", E_USER_WARNING);
	}

	$group_select = '<select name="' . $select_name . '">';
	
	$group_name = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_rows > 0)
	{
		$group_name[$num_rows]['group_id'] = 0;
		$group_name[$num_rows]['group_name'] = '没有指定';

		for ($i = 0; $i < count($group_name); $i++)
		{
			if (!$default_group)
			{
				$selected = ($i == 0) ? ' selected="selected"' : '';
			}
			else
			{
				$selected = ($group_name[$i]['group_id'] == $default_group) ? ' selected="selected"' : '';
			}

			$group_select .= '<option value="' . $group_name[$i]['group_id'] . '"' . $selected . '>' . $group_name[$i]['group_name'] . '</option>';
		}
	}

	$group_select .= '</select>';

	return $group_select;
}

function download_select($select_name, $group_id = 0)
{
	global $db, $types_download, $modes_download;
		
	if ($group_id)
	{
		$sql = 'SELECT download_mode
			FROM ' . EXTENSION_GROUPS_TABLE . '
			WHERE group_id = ' . (int) $group_id;

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error("Couldn't query Extension Groups Table", E_USER_WARNING);
		}
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		if (!isset($row['download_mode']))
		{
			return '';
		}
		
		$download_mode = $row['download_mode'];
	}

	$group_select = '<select name="' . $select_name . '">';

	for ($i = 0; $i < count($types_download); $i++)
	{
		if (!$group_id)
		{
			$selected = ($types_download[$i] == INLINE_LINK) ? ' selected="selected"' : '';
		}
		else
		{
			$selected = ($row['download_mode'] == $types_download[$i]) ? ' selected="selected"' : '';
		}

		$group_select .= '<option value="' . $types_download[$i] . '"' . $selected . '>' . $modes_download[$i] . '</option>';
	}

	$group_select .= '</select>';

	return $group_select;
}

function category_select($select_name, $group_id = 0)
{
	global $db, $types_category, $modes_category;
		
	$sql = 'SELECT group_id, cat_id
		FROM ' . EXTENSION_GROUPS_TABLE;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error("Couldn't select Category", E_USER_WARNING);
	}
	
	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	$type_category = 0;

	if ($num_rows > 0)
	{
		for ($i = 0; $i < $num_rows; $i++)
		{
			if ($group_id == $rows[$i]['group_id'])
			{
				$category_type = $rows[$i]['cat_id'];
			}
		}
	}

	$types = array(NONE_CAT);
	$modes = array('没有');

	for ($i = 0; $i < count($types_category); $i++)
	{
		$types[] = $types_category[$i];
		$modes[] = $modes_category[$i];
	}

	$group_select = '<select name="' . $select_name . '" style="width:100px">';

	for ($i = 0; $i < count($types); $i++)
	{
		if (!$group_id)
		{
			$selected = ($types[$i] == NONE_CAT) ? ' selected="selected"' : '';
		}
		else
		{
			$selected = ($types[$i] == $category_type) ? ' selected="selected"' : '';
		}

		$group_select .= '<option value="' . $types[$i] . '"' . $selected . '>' . $modes[$i] . '</option>';
	}

	$group_select .= '</select>';

	return $group_select;
}

function size_select($select_name, $size_compare)
{

	$size_types_text 	= array('Bytes', 'KB', 'MB');
	$size_types 		= array('b', 'kb', 'mb');

	$select_field = '<select name="' . $select_name . '">';
	
	for ($i = 0; $i < count($size_types_text); $i++)
	{
		$selected = ($size_compare == $size_types[$i]) ? ' selected="selected"' : '';
		$select_field .= '<option value="' . $size_types[$i] . '"' . $selected . '>' . $size_types_text[$i] . '</option>';
	}
	
	$select_field .= '</select>';

	return $select_field;
}

function quota_limit_select($select_name, $default_quota = 0)
{
	global $db;
		
	$sql = 'SELECT quota_limit_id, quota_desc
		FROM ' . QUOTA_LIMITS_TABLE . '
		ORDER BY quota_limit ASC';

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error("Couldn't query Quota Limits Table", E_USER_WARNING);
	}

	$quota_select = '<select name="' . $select_name . '">';
	$quota_name[0]['quota_limit_id'] = 0;
	$quota_name[0]['quota_desc'] = '不可用';

	while ($row = $db->sql_fetchrow($result))
	{
		$quota_name[] = $row;
	}
	$db->sql_freeresult($result);

	for ($i = 0; $i < count($quota_name); $i++)
	{
		$selected = ($quota_name[$i]['quota_limit_id'] == $default_quota) ? ' selected="selected"' : '';
		$quota_select .= '<option value="' . $quota_name[$i]['quota_limit_id'] . '"' . $selected . '>' . $quota_name[$i]['quota_desc'] . '</option>';
	}
	$quota_select .= '</select>';

	return $quota_select;
}

function default_quota_limit_select($select_name, $default_quota = 0)
{
	global $db;
		
	$sql = 'SELECT quota_limit_id, quota_desc
		FROM ' . QUOTA_LIMITS_TABLE . '
		ORDER BY quota_limit ASC';

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error("Couldn't query Quota Limits Table", E_USER_WARNING);
	}

	$quota_select = '<select name="' . $select_name . '">';
	$quota_name[0]['quota_limit_id'] = 0;
	$quota_name[0]['quota_desc'] = '没有限制';

	while ($row = $db->sql_fetchrow($result))
	{
		$quota_name[] = $row;
	}
	$db->sql_freeresult($result);

	for ($i = 0; $i < count($quota_name); $i++)
	{
		$selected = ( $quota_name[$i]['quota_limit_id'] == $default_quota ) ? ' selected="selected"' : '';
		$quota_select .= '<option value="' . $quota_name[$i]['quota_limit_id'] . '"' . $selected . '>' . $quota_name[$i]['quota_desc'] . '</option>';
	}
	$quota_select .= '</select>';

	return $quota_select;
}

?>