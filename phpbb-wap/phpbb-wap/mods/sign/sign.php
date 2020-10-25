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

/*
* MOD名称: 签到插件
* MOD支持地址: http://phpbb-wap.com
* MOD描述: 你多少天没签到了？
* MOD作者: Crazy
* MOD版本: v6.0
* MOD显示: on
*/

// 如果用户没有登录，则跳转到登录页面
if ( !$userdata['session_logged_in'] )
{
	login_back('loading.php?mod=sign');
}

define('SIGN_TABLE', $table_prefix.'sign');

require ROOT_PATH . 'includes/functions/selects.php';
require ROOT_PATH . 'includes/functions/bbcode.php';

// 签到可以获得多少金币
$sign_points = 10;

// 每页显示多少条内容
$per = 10;

$start = get_pagination_start($per);

$sql = 'SELECT sign_id 
	FROM ' . SIGN_TABLE;
	
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$total_all_sign = $db->sql_numrows($result);

$pagination = ( $total_all_sign <= 0 ) ? '' : generate_pagination('loading.php?mod=sign', $total_all_sign, $per, $start);

$sql = "SELECT s.*, u.user_id, u.username
	FROM phpbb_sign s, phpbb_users u
	WHERE u.user_id = s.sign_user_id
	ORDER BY s.sign_id DESC
	LIMIT $start, $per";

if ( !$result = $db->sql_query($sql) )
{
	trigger_error('数据查询失败', E_USER_WARNING);
}

$sign_rows = array();

while ($row = $db->sql_fetchrow($result))
{
	$sign_rows[] = $row;
}

$total_sign_rows = count($sign_rows);

for($i = 0; $i < $total_sign_rows; $i++)
{
	$number = $i + 1 + $start;
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('sign_rows', array(
		'NUMBER'				=> $number,
		'ROW_CLASS' 			=> $row_class,
		'SIGN_USERNAME' 		=> $sign_rows[$i]['username'],
		'U_SIGN_VIEWPROFILE' 	=> append_sid("ucp.php?mode=viewprofile&u=" . $sign_rows[$i]['sign_user_id']),
		'SIGN_TIME' 			=> create_date($userdata['user_dateformat'], $sign_rows[$i]['sign_time'], $board_config['board_timezone']),
		'SIGN_TALK' 			=> smilies_pass($sign_rows[$i]['sign_talk']))
	);
}

$lingchen_time = strtotime(create_date('y-m-d', time(), $board_config['board_timezone']));

$sql = "SELECT u.user_id, u.username
	FROM phpbb_sign s, phpbb_users u
	WHERE u.user_id = s.sign_user_id
		AND s.sign_time >= $lingchen_time
	ORDER BY s.sign_time ASC
	LIMIT 3";

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获取今日签到之星', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('ago_user', array(
		'NUMBER' => $i + 1,
		'ROW_CLASS' => $row_class,
		'USERNAME'	=> $row['username'],
		'U_UCP' => append_sid("ucp.php?mode=viewprofile&u=" . $row['username']),
		//'SIGN_TIME' => create_date('H:i', $row['sign_time'], $board_config['board_timezone'])
		)
	);
	$i++;
}

/*
$ago_user_list = array();

foreach ($sign_rows as $key => $value)
{
	$lingchen_time = strtotime(create_date('y-m-d', time(), $board_config['board_timezone']));

	if ($value['sign_time'] >= $lingchen_time)
	{
		$
	}
}
*/

date_default_timezone_set('PRC');
$lingchen = strtotime('today'); 
$sql = "SELECT * 
   FROM phpbb_sign 
   WHERE sign_user_id = " . $userdata['user_id'] . " 
      AND sign_time > $lingchen";

if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

if ( $db->sql_numrows($result) >= 1 )
{
	$template->assign_block_vars('switch_yes_sign', array());
}
else
{
	
	$confirm = ( isset($_POST['confirm']) ) ? true : false;
	$user_id = $userdata['user_id'];
	$time = time();
	$talk = ( isset($_POST['smile_code']) ) ? htmlspecialchars($_POST['smile_code']) : '';
	$talk .= ( isset($_POST['talk']) ) ? htmlspecialchars($_POST['talk']) : false;
	if ( $confirm )
	{
		if ( $user_id && $time && $talk )
		{
			$sql = "INSERT INTO phpbb_sign (sign_user_id, sign_time, sign_talk) 
				VALUES (" . str_replace("\'", "''", $user_id) . ", " . str_replace("\'", "''", $time) . ", '" . str_replace("\'", "''", $talk) . "')";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('插入签到信息', E_USER_WARNING);
			}
			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = user_points + $sign_points
				WHERE user_id = " . $user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('无法增加签到 points', E_USER_WARNING);
			}
			trigger_error('签到成功！<br />点击 <a href="' . append_sid('loading.php?mod=sign') . '">这里</a> 签到页面');
		}
		else
		{
			trigger_error('请指定内容和选项！', E_USER_ERROR);
		}
	}
	
	$template->assign_block_vars('switch_no_sign', array());
}

page_header('签到');

$template->set_filenames(array(
	'body' => 'sign_body.tpl')
);

$hidden_fields = '<input type="hidden" name="confirm" value="yes" />';
$smiles_select = smiles_select();

$template->assign_vars(array(
	'SIGN_USERNAME'				=> $userdata['username'],
	'S_PROFILE_ACTION'			=> append_sid('loading.php?mod=sign'),
	'PAGINATION'				=> $pagination,
	'ADD_POINTS'				=> $sign_points,
	'POINTS_NAME'				=> $board_config['points_name'],
	'SMILES_SELECT' 			=> $smiles_select,
	'S_HIDDEN_FORM_FIELDS' 		=> $hidden_fields)
);

$template->pparse('body');
page_footer();
?>