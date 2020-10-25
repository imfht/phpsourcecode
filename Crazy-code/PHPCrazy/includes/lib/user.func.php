<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

////////////////////////////// 会员 /////////////////////////////////

/*
* 	生成一个唯一的SID
* 	mksid(用户ID, 用户名, 密码)
*/
function mksid($id, $name, $pass) {
	return strtr(base64_encode(str_shuffle(base64_encode(md5(md5($name,true).md5(microtime(),true).md5($pass,true),true))).base64_encode(pack('V',$id))), array('+'=>'-', '/'=>'_','='=> ''));
}

/*
*	注册一个用户
*	RegisterUser(用户名, Email, 密码)
*/
function RegisterUser($username, $email, $password2) {
	
	global $PDO;

	$sql = 'SELECT max(id) as maxid
		FROM ' . USERS_TABLE;

	$result = $PDO->query($sql);

	$maxid = $result->fetchColumn();

	$new_UserId = $maxid + 1;

	$newUserSid = mksid($new_UserId, $username, $password2);

	$md5_password = ($password2 == '') ? '' : md5($password2);

	$sql = 'INSERT INTO ' . USERS_TABLE . ' (id, username, password, email, sid, regtime)
		VALUES (:id, :username, :password, :email, :sid, :regtime)';

	$result = $PDO->prepare($sql);

	$NewUserData = array(
		':id' => $new_UserId,
		':username' => $username,
		':password' => $md5_password,
		':email' => $email,
		':sid' => $newUserSid,
		':regtime' => time()
	);

	$result->execute($NewUserData);

	return $NewUserData;
}

/*
* 	验证用户名是否合法
*	Vusername(用户名, 旧用户名)
*/
function Vusername($username, $oldUsername = '') {

	global $PDO;

	if ($username == '') {

		return array('error' => true, 'error_msg' => L('！用户名不能为空'));
	}

	if (!preg_match('/[^0-9]/', $username)) {

		return array('error' => true, 'error_msg' => L('！用户名不能为全数字'));
	}

	// 去除空格
	$username = preg_replace('#\s+#', ' ', trim($username));
	
	// 非法字符
	$illegal_username = array('*', '"', '<', '>', '-', ';', '=', ',', '`', '&', '#', '(', ')', "\\", '%', '$');
	
	foreach ($illegal_username as $illegal_value) {
		
		if (strstr($username, $illegal_value)) {

			return array('error' => true, 'error_msg' => L('！用户名带有非法字符'));
		}
	}

	// 用户名小于12字符
	if (mb_strlen($username, 'UTF-8') > 12) {

		return array('error' => true, 'error_msg' => L('！用户名太长'));
	}

	if ($oldUsername != $username) {

		// 已注册的用户名
		$sql = 'SELECT id, username
			FROM ' . USERS_TABLE . '
			WHERE username = :username';

		$result = $PDO->prepare($sql);

		$result->execute(array(':username' => $username));

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			if ($row['username'] == $username) {

				return array('error' => true, 'error_msg' => L('！用户名已存在'));

			}

		}
	}


	return array('error' => false, 'error_msg' => '');
}

/*
* 	验证电子邮件地址
*	Vemail(用户名, 旧用户名)
*/
function Vemail($email, $oldEmail = '') {

	global $PDO;

	if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email)) {
		return array('error' => true, 'error_msg' => L('邮箱无效'));
	}
	
	if ($oldEmail != $email) {

		$sql = 'SELECT email 
			FROM ' . USERS_TABLE . '
			WHERE email = :email';

		$result = $PDO->prepare($sql);

		$result->execute(array(':email' => $email));

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			
			if ($row['email'] == $email) {
				
				return array('error' => true, 'error_msg' => L('邮箱已注册'));
			
			}
		}
	}

	return array('error' => false, 'error_msg' => '');
}

?>