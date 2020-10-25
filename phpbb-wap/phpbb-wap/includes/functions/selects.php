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


/**
* 选择风格
**/
function style_select($default_style, $style_data, $select_name = "style")
{

	$style_select = '<select name="' . $select_name . '">';
	foreach ($style_data as $style_id => $value)
	{
		$selected = ( $style_id == $default_style ) ? ' selected="selected"' : '';
		$style_select .= '<option value="' . $style_id . '"' . $selected . '>' . $value['name'] . '</option>';
	}
	
	$style_select .= "</select>";

	return $style_select;
}

/**
* 时区选择
**/
function tz_select($default, $select_name = 'timezone')
{
	global $sys_timezone;

	if ( !isset($default) )
	{
		$default == $sys_timezone;
	}
	$tz_select = '<select name="' . $select_name . '">';
	$tz = array(
		'-12' => 'UTC - 12 小时','-11' => 'UTC - 11 小时', '-10' => 'UTC - 10 小时', '-9.5' => 'UTC - 9:30 小时',
		'-9' => 'UTC - 9 小时', '-8' => 'UTC - 8 小时', '-7' => 'UTC - 7 小时', '-6' => 'UTC - 6 小时',
		'-5' => 'UTC - 5 小时', '-4.5' => 'UTC - 4:30 小时', '-4' => 'UTC - 4 小时', '-3.5' => 'UTC - 3:30 小时',
		'-3' => 'UTC - 3 小时', '-2' => 'UTC - 2 小时', '-1' => 'UTC - 1 小时', '0' => 'UTC + 0 [格林威治]',
		'1' => 'UTC + 1 小时', '2' => 'UTC + 2 小时', '3' => 'UTC + 3 小时', '3.5' => 'UTC + 3:30 小时',
		'4' => 'UTC + 4 小时', '4.5' => 'UTC + 4:30 小时', '5' => 'UTC + 5 小时', '5.5' => 'UTC + 5:30 小时',
		'5.75' => 'UTC + 5:45 小时', '6' => 'UTC + 6 小时', '6.5' => 'UTC + 6:30 小时', '7' => 'UTC + 7 小时',
		'8' => 'UTC + 8 [北京时间]', '8.75' => 'UTC + 8:45 小时', '9' => 'UTC + 9 小时', '9.5' => 'UTC + 9:30 小时',
		'10' => 'UTC + 10 小时', '10.5' => 'UTC + 10:30 小时', '11' => 'UTC + 11 小时', '11.5' => 'UTC + 11:30 小时',
		'12' => 'UTC + 12 小时', '12.75' => 'UTC + 12:45 小时', '13' => 'UTC + 13 小时', '14' => 'UTC + 14 小时'
		//'dst' => '[ <abbr title="夏令时">DST</abbr> ]',
	);
	foreach($tz as $offset => $zone)
	{
		$selected = ( $offset == $default ) ? ' selected="selected"' : '';
		$tz_select .= '<option value="' . $offset . '"' . $selected . '>' . $zone . '</option>';
	}
	$tz_select .= '</select>';

	return $tz_select;
}

/**
* 日期格式选择
**/
function select_dateformat($dateformat, $name)
{
	global  $userdata;
	$tz_dateformat = array("Y/m/d", "Y/m/d G:i", "Y/m/d, H:i (l)", "Y/m/d, H:i (D)", "Y年m月d日", "Y年m月d日 H:i", "Y年m月d日 H:i (l)", "Y年m月d日 H:i (D)", "Y-m-d", "Y-m-d H:i", "Y-m-d H:i (l)", "Y-m-d H:i (D)");
	$select_date_format = '<select name="' . $name . '">' . "\n";
	foreach ($tz_dateformat as $k => $v)
	{
		$select_date_format .= '<option value="' . $v . '"' . ($v == $dateformat ? 'selected="selected"' : '') . '>';
		$select_date_format .= create_date($v, time(), $userdata['user_timezone'], false) . '</option>' . "\n";
	}
	$select_date_format .= '</select>';
	return $select_date_format;
}

/**
* 表情选择
**/
function smiles_select()
{
	global $db;

	$sql = "SELECT code
		FROM " . SMILIES_TABLE . " 
		ORDER BY smilies_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('表情获取失败', E_USER_WARNING);
	}

	$smiles_select = '<select name="smile_code">';
	$smiles_select .= '<option value=""> </option>';
	while ( $row = $db->sql_fetchrow($result) )
	{
		$smiles_select .= '<option value="' . $row['code'] . '">' . $row['code'] . '</option>';
	}
	$smiles_select .= "</select>";

	return $smiles_select;
}
?>