<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

/**
* 用户权限类
*/
class Auth
{
	
	var $master;
	var $admin;
	var $user;
	var $anonymous;

	var $Auth;

	function __construct() {

		$this->master = array(MASTER => true, ADMIN => true, USER => true, ANONYMOU => true);
		$this->admin = array(MASTER => false, ADMIN => true, USER => true, ANONYMOU => true);
		$this->user = array(MASTER => false, ADMIN => false, USER => true, ANONYMOU => true);
		$this->anonymous = array(MASTER => false, ADMIN => false, USER => false, ANONYMOU => true);

	}

	function IsAuth($User) {

		global $PDO;

		$sql = 'SELECT auth 
			FROM ' . USERS_TABLE . '
			WHERE id = :id';

		$result = $PDO->prepare($sql);

		$result->execute(array(':id' => (int)$User));

		if (!$row = $result->fetch(PDO::FETCH_ASSOC)) {
			return $this->anonymous;
		}

		switch ($row['auth']) {
			
			case MASTER: 
				$this->Auth = $this->master;
			break;
			case ADMIN:
				$this->Auth = $this->admin;
				break;
			case USER:
				$this->Auth = $this->user;
				break;
			case ANONYMOU:
				$this->Auth = $this->anonymous;
				break;
			default:
				$this->Auth = $this->anonymous;
				break;
		}

		return $this->Auth;

	}

}
?>