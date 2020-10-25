<?php
/**
 * User: weicaihong.com
 * Date: 14-10-14 17:14
 * Copyright: http://www.weicaihong.com
 */

// 表前缀 $prefix
$tb_users = $prefix.'users';
$keyword = $post_data['keyword'];
$inputArray = explode(",",$keyword);
$method = $inputArray[0];

//输出数组
$data = array();

//第一次关注
if($method == "firstPayAttention"){
	//微信id号
	$wxid = $inputArray[1];
	//用户名前缀
	$userNamePrefix = $inputArray[2];
	//密码位数
	$pwdLength = $inputArray[3];
	//2015-03-24 增加推荐人id参数
	$parentId = $inputArray[4];

	$query_sql = "SELECT user_id,user_name FROM `$tb_users` WHERE `wxid` = '$wxid' ";

	// 查询sql
	$sth = $pdo_db->prepare($query_sql);
	$sth->execute();

	$dataTmp = $sth->fetchAll(PDO::FETCH_ASSOC);
	$dataTmp['num'] = count($dataTmp);

	//用户不存在，注册
	if($dataTmp['num'] == 0){
		//注册
		//		define('IN_ECS', true);
		//		require(dirname(__FILE__) . '/../includes/init.php');
		$randNum = randNum($pwdLength);
		//修改用户名生成方式为  用户名前缀+时间戳+随机数
		$username = $userNamePrefix.time().$randNum;
		$password = randNum($pwdLength);
		$email = $username."@null.null";
		//将该用户名插入到数据库中
		if($GLOBALS['user']->add_user($username, $password, $email)){
			
			//插入成功后更新用户名为 前缀+user_id
			$query_sql = "SELECT user_id FROM `$tb_users` WHERE `user_name` = '$username' ";

			// 重新获取用户
			$sth = $pdo_db->prepare($query_sql);
			$sth->execute();
			$dataTmp = $sth->fetchAll(PDO::FETCH_ASSOC);
			$user_id = $dataTmp[0]['user_id'];
			
			$usernameT = $username;
			$emailT = $email;
			
			//重新赋值用户名和email
			$username = $userNamePrefix.$user_id;
			$email = $username."@null.null";
			
			$data['error'] = "0";
			$data['num'] = $dataTmp['num'];
			$data["username"] = $username;
			$data["password"] = $password;
			$data["email"] = $email;
			// 根据user_id更新微信id和用户名
			$query_sql = "update `$tb_users` set wxid = '$wxid',parent_id = '$parentId',user_name='$username',email='$email' WHERE `user_id` = '$user_id' ";
			$sth = $pdo_db->prepare($query_sql);
			$result = $sth->execute();
			//更新异常则使用原随机的用户名
			if(!$result){
				$query_sql = "update `$tb_users` set wxid = '$wxid',parent_id = '$parentId' WHERE `user_name` = '$usernameT' ";
				$sth = $pdo_db->prepare($query_sql);
				$result = $sth->execute();
				$data["username"] = $usernameT;
				$data["email"] = $emailT;
			}
				
			//获取用户id
//			$query_sql = "SELECT user_id,user_name FROM `$tb_users` WHERE `wxid` = '$wxid' ";
//			// 查询sql
//			$sth = $pdo_db->prepare($query_sql);
//			$sth->execute();
//			$dataTmp = $sth->fetchAll(PDO::FETCH_ASSOC);
			$data["userId"] = $user_id;

		}else{
			if ($GLOBALS['user']->error == ERR_INVALID_USERNAME)
			{
				$data['error'] = "ERR_INVALID_USERNAME!";
			}
			elseif ($GLOBALS['user']->error == ERR_USERNAME_NOT_ALLOW)
			{
				$data['error'] = "ERR_USERNAME_NOT_ALLOW!";
			}
			elseif ($GLOBALS['user']->error == ERR_USERNAME_EXISTS)
			{
				$data['error'] = "ERR_USERNAME_EXISTS!";
			}
			elseif ($GLOBALS['user']->error == ERR_INVALID_EMAIL)
			{
				$data['error'] = "ERR_INVALID_EMAIL!";
			}
			elseif ($GLOBALS['user']->error == ERR_EMAIL_NOT_ALLOW)
			{
				$data['error'] = "ERR_EMAIL_NOT_ALLOW!";
			}
			elseif ($GLOBALS['user']->error == ERR_EMAIL_EXISTS)
			{
				$data['error'] = "ERR_EMAIL_EXISTS!";
			}
			else
			{
				$data['error'] = 'UNKNOWN ERROR!';
			}
		}
	}else{
		$data['num'] = $dataTmp['num'];
		$data["userId"] = $dataTmp[0]['user_id'];
		$data["username"] = $dataTmp[0]['user_name'];
	}

}

/*
 * 随机数
 * */
function randNum($num = 6){
	$returnValue = "";
	for($i=0;$i<$num;$i++){
		$ranNum = rand(0,9);
		$returnValue = $returnValue.$ranNum;
	}
	return $returnValue;
}

// 转换为json
$json_data = json_encode($data);

// 全部数据以UTF8 编码
if($ec_charset != 'UTF8')
{
	$json_data = mb_convert_encoding($json_data,'UTF-8','GBK');
}

// 输出
require('wch_json.php');

