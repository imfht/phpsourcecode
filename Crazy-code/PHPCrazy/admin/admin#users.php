<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!defined('IN_PHPCRAZY') || !defined('IN_ADMIN')) exit;

if (isset($setModule)) {

	return $Module['Users'][L('用户管理')] = 'users';

}

LoadFunc('misc');

$mode = isset($_GET['mode']) ? $_GET['mode'] : '';

if ($mode == 'info') {

	if(!isset($_GET['user'])) {

		Message(WARNING, L('提示'), L('请指定用户'));
	}

	if (empty($_GET['user'])) {
		
		Message(WARNING, L('提示'), L('请指定用户'));
	}

	$user = abs(intval($_GET['user']));
		
	$sql = 'SELECT * 
		FROM ' . USERS_TABLE . '
		WHERE id = ' . $user;

	$result = $PDO->query($sql);

	$row = $result->fetch(PDO::FETCH_ASSOC);

	if (!$row) {
		
		Message(INFO, L('提示'), L('用户不存在'));
	}

	LoadFunc('user');

	$submit = isset($_POST['submit']) ? true : false;
	
	$continue = true;
	$error = array();

	if ($submit) {
		
		$password = isset($_POST['password']) ? $_POST['password'] : '';

		if (empty($password)) {

			$password = $row['password'];
		} else {
			$password = md5($_POST['password']);
		}

		$post_list = array(
			'username', 'email', 'auth'
		);

		$PostVars = array();

		foreach ($post_list as $post_name) {
			
			$PostVars[$post_name] = isset($_POST[$post_name]) ? $_POST[$post_name] : '';
		}

		if ($GLOBALS['U']['auth'] == ADMIN) {
			
			// 管理员不能修改主人的资料
			if ($row['auth'] == MASTER) {

				Message(INFO, L('提示'), L('权限不足'));
			}

			// 管理员不能将会员的权限设置为主人权限
			if ($PostVars['auth'] == MASTER) {
				
				Message(INFO, L('提示'), L('权限不足'));
			}

		}

		$result = Vusername($PostVars['username'], $row['username']);
		
		if ($result['error']) {
			$continue = false;
			$error[] = $result['error_msg'];
		}

		$result = Vemail($PostVars['email'], $row['email']);

		if ($result['error']) {
			$continue = false;
			$error[] = $result['error_msg'];
		}

		$sql = 'UPDATE ' . USERS_TABLE . ' 
			SET username = :username,
				password = :password,
				auth = :auth,
				email = :email
			WHERE id = :id';

		$result = $PDO->prepare($sql);

		$result->execute(
			array(
				':username' => $PostVars['username'],
				':password' => $password,
				':auth'	=> (int)$PostVars['auth'],
				':email' => $PostVars['email'],
				':id' => $user
			)
		);

		// 重置一些变量
		$row['username'] = $PostVars['username'];

		$row['auth'] = $PostVars['auth'];

		$row['email'] = $PostVars['email'];

	}

	$PageTitle = sprintf(L('标题 模块名 后台'), L($row['username']), L('用户管理'));

	include T('user');

	AppEnd();

} elseif($mode == 'search') {

	$Keyword = isset($_POST['k']) ? $_POST['k'] : '';

	if (empty($Keyword)) {
		
		Message(INFO, L('提示'), L('关键词为空'));
	}

	if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $Keyword)) {
		
		$sql = 'SELECT id
			FROM ' . USERS_TABLE . '
			WHERE email = :keyword';

	} elseif (preg_match('/^[1-9][0-9]{4,9}$/', $Keyword)) {
		
		$sql = 'SELECT id
			FROM ' . USERS_TABLE . '
			WHERE id = :keyword';

	} else {
		
		$sql = 'SELECT id
			FROM ' . USERS_TABLE . '
			WHERE username = :keyword';
	}

		
	$result = $PDO->prepare($sql);

	$result->execute(array(':keyword' => $Keyword));

	if (!$row = $result->fetch(PDO::FETCH_ASSOC)) {
		
		Message(INFO, L('提示'), L('用户不存在'));
	}

	header('Location: ' . AdminActionUrl('users&mode=info&user=' . $row['id']));

	AppEnd();
}


$PageTitle = sprintf(L('模块名 后台'), L('用户管理'));

$per = 10;
$start = InitStart($per);

$sql = 'SELECT id, username 
	FROM ' . USERS_TABLE . '
	WHERE id <> ' . ANONYMOUS . '
	ORDER BY regtime DESC
	LIMIT ' . $start . ', ' . $per;

$result = $PDO->query($sql);

$UserList = $result->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT count(id) as total 
	FROM ' . USERS_TABLE . '
	WHERE id <> ' . ANONYMOUS;

$result = $PDO->query($sql);

$row = $result->fetch(PDO::FETCH_ASSOC);

$total = $row['total'];

$P = new Pagination('action=users', $total, $start, $per, true);

include T('users');

AppEnd();

?>