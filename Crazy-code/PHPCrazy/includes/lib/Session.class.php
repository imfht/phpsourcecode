<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

/**
*	Session 类
*/
class Session 
{
	var $session_id = '';
	var $cookie_name = 'Crazy_sid';
	var $cookie_expire = 3600;
	var $cookie_path = '/';
	var $cookie_domain = '';
	var $cookie_secure = false;
	var $time;

	function __construct() {
		$this->session_id = isset($_COOKIE[$this->cookie_name]) ? $_COOKIE[$this->cookie_name] : '';
		$this->cookie_domain = $_SERVER['HTTP_HOST'];
		$this->time = time();
	}

	function Init() {
		
		global $PDO;

		$UserData = array(
			'id' => 0, 
			'username' => sprintf(L('游客 随机数'), rand(1000, 9999)), 
			'email' => '', 
			'regtime' => time(),
			'login' => false,
			'auth' => 0
		);

		if (empty($this->session_id)) {
			return $UserData;
		}

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE sid = :sid';

		$result = $PDO->prepare($sql);

		$result->execute(array(':sid' => $this->session_id));

		if ($row = $result->fetch(PDO::FETCH_ASSOC)) {

			$UserData = array_merge($row, array('login' => true));

			// 防止在访问的过程中出现自动退出的情况
			$this->Login($this->session_id);

			return $UserData;
		}

		return $UserData;
	}

	function Login($sid) {

		setcookie($this->cookie_name, $sid, ($this->time + $this->cookie_expire), $this->cookie_path, $this->cookie_domain, $this->cookie_secure);
	
	}

	function Logout() {

		setcookie($this->cookie_name, $sid, ($this->time - $this->cookie_expire), $this->cookie_path, $this->cookie_domain, $this->cookie_secure);
	
	}
}

?>