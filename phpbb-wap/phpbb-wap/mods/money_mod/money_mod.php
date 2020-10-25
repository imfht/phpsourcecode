<?php

/**
 * @package phpBB-WAP
 * @简体中文：中文phpBB-WAP团队
 * @license http://opensource.org/licenses/gpl-license.php
 * */
/**
 * 这是一款自由软件, 您可以在 Free Software Foundation 发布的
 * GNU General Public License 的条款下重新发布或修改; 您可以
 * 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
 * 你喜欢）作为新的牌照.
 * */
/*
 * MOD名称: 金币管理
 * MOD支持地址: http://www.zisuw.com
 * MOD描述: 金币交易、管理发放工资、增删会员金币等
 * MOD作者: kill、Crazy
 * MOD版本: v6.0
 * MOD显示: on
 */

/*
* 统治阶级们注意啦，当您选择对象为版主、管理员的情况下
* 他们的金币不足的时候会产生负数的金币哦
*/

define('MONEY_MOD_USER', 0);
define('MONEY_MOD_ADMIN', 1);
define('MONEY_MOD_MOD', 2);

define('MONEY_MOD_ADD', 0);
define('MONEY_MOD_CUT', 1);

// 检查用户是否有权限操作
if ($userdata['user_level'] != ADMIN)
{
	trigger_error('抱歉，您没有相关权限！');
}

if (isset($_POST['submit']))
{
	// 提交的信息
	$user_id 	= (isset($_POST['user_id'])) ? abs(intval($_POST['user_id'])) : 0;// phpBB-WAP中是没有0为ID的用户的，-1是游客、负数ID是不存在的，因此忽略。
	$object		= (isset($_POST['object'])) ? $_POST['object'] : MONEY_MOD_USER;
	$number 	= (isset($_POST['number'])) ? abs(intval($_POST['number'])) : 0;
	$action 	= (isset($_POST['action'])) ? $_POST['action'] : MONEY_MOD_ADD;

	if (($action == MONEY_MOD_ADD) || ($action == MONEY_MOD_CUT))
	{
		$action = $action;
	}
	else
	{
		$action = MONEY_MOD_ADD;
	}

	switch ($object)
	{
		// 任意用户
		case MONEY_MOD_USER:
			if (!$set_user = get_userdata($user_id))
			{
				trigger_error('用户不存在');
			}

			if ($action == MONEY_MOD_ADD)
			{
				$new_points = $set_user['user_points'] + $number;
			}
			else
			{
				if ($set_user['user_points'] < $number)
				{
					trigger_error('Ta只有' . $set_user['user_points'] . ' ' . $board_config['points_name'] . '哦，残忍的统治阶级。。。');
				}

				$new_points = $set_user['user_points'] - $number;
			}

			$sql = 'UPDATE ' . USERS_TABLE . ' 
				SET user_points = ' . $new_points . '
				WHERE user_id = ' . $user_id;

			if (!$db->sql_query($sql))
			{
				trigger_error('无法查询 ' . USERS_TABLE . ' 表', E_USER_WARNING);
			}

			if ($action == MONEY_MOD_ADD)
			{
				$message = '已增加 ' . $set_user['username'] . ' ' . $number . ' ' . $board_config['points_name'] . '，Ta目前有 ' . $new_points . ' ' . $board_config['points_name'];
			}
			else
			{
				$message = '已扣取 ' . $set_user['username'] . ' ' . $number . ' ' . $board_config['points_name'] . '，Ta目前有 ' . $new_points . ' ' . $board_config['points_name'];
			}

			trigger_error($message);
			break;
		// 所有版主
		case MONEY_MOD_MOD:

			// 因为版主的数量是一个未知数
			@set_time_limit(0);
			
			if ($action == MONEY_MOD_ADD)
			{
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_points = user_points + ' . $number . '
					WHERE user_level = ' . MOD;
			}
			else
			{
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_points = user_points - ' . $number . '
					WHERE user_level = ' . MOD;
			}

			if (!$db->sql_query($sql))
			{
				trigger_error('无法查询 ' . USERS_TABLE . ' 表', E_USER_WARNING);
			}

			if ($action == MONEY_MOD_ADD)
			{
				$message = '已为所有版主增加了 ' . $number . ' ' . $board_config['points_name'];
			}
			else
			{
				$message = '已扣取所有版主 ' . $number . ' ' . $board_config['points_name'];
			}
			
			trigger_error($message);

			break;
		// 所有管理员
		case MONEY_MOD_ADMIN:

			// 因为管理员的数量也是一个未知数
			@set_time_limit(0);

			if ($action == MONEY_MOD_ADD)
			{
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_points = user_points + ' . $number . '
					WHERE user_level = ' . ADMIN;
			}
			else
			{
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_points = user_points - ' . $number . '
					WHERE user_level = ' . ADMIN;
			}

			if (!$db->sql_query($sql))
			{
				trigger_error('无法查询 ' . USERS_TABLE . ' 表', E_USER_WARNING);
			}

			if ($action == MONEY_MOD_ADD)
			{
				$message = '已为所有管理员增加了 ' . $number . ' ' . $board_config['points_name'];
			}
			else
			{
				$message = '已扣取所有管理员 ' . $number . ' ' . $board_config['points_name'];
			}
			
			trigger_error($message);
			break;
		default:
			// 似乎出错了
			redirect(append_sid('index.php', true));
			break;
	}
}

page_header();

$template->assign_vars(array(
	'S_ACTION' => append_sid('loading.php?mod=money_mod'))
);

$template->set_filenames(array(
	'body' => 'money_mod_body.tpl')
);

$template->pparse('body');

page_footer();
?>