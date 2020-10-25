<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* 专题的下拉框
*/
function class_select($forum_id, $topic_id, $default_class)
{
	global $db;
	
	$sql = 'SELECT class_id, class_name
		FROM ' . CLASS_TABLE . ' 
		WHERE class_forum = ' . $forum_id;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('读取专题信息失败！', E_USER_WARNING);
	}
	
	$class_select = '<form action="' . append_sid('viewclass.php?mode=select&' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_TOPIC_URL . "=$topic_id") . '" method="post">';
	$class_select .= '专题：';
	$class_select .= '<select name="' . POST_CLASS_URL . '">';
	$class_select .= '<option value="' . TOPIC_UNCLASS . '">不设置专题</option>';
	
	while ( $row = $db->sql_fetchrow($result) )
	{
		$selected = ( $row['class_id'] == $default_class ) ? ' selected="selected"' : '';
		$class_select .= '<option value="' . $row['class_id'] . '"' . $selected . '>' . $row['class_name'] . '</option>';
	}

	$class_select .= '</select>';
	$class_select .= '<input type="submit" value="设置" />';
	$class_select .= '</form>';

	return $class_select;
}

/*
* 获取专题
*/
function default_topic_class($topic_id)
{
	global $db;

	$sql = 'SELECT topic_class 
		FROM ' . TOPICS_TABLE . ' 
		WHERE topic_id = ' . $topic_id;
		
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('获取专题信息失败！', E_USER_WARNING);
	}

	if ( $topic_class = $db->sql_fetchrow($result) )
	{
		return $topic_class['topic_class'];
	}

	return TOPIC_UNCLASS;
}

function main_topic_class($forum_id, $default_class)
{
	global $db;

	$sql = 'SELECT class_name
		FROM ' . CLASS_TABLE . ' 
		WHERE class_id = ' . $default_class;

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('读取专题信息失败！', E_USER_WARNING);
	}

	if ($class_data = $db->sql_fetchrow($result))
	{
		$class_link = '<div>[专题]：<a href="' . append_sid('viewclass.php?' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_CLASS_URL . '=' . $default_class) . '">' . $class_data['class_name'] . '</a></div>';
	}
	else
	{
		$class_link = '[专题]：没有指定';
	}

	return $class_link;
}


?>