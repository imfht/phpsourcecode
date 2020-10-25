<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>, 李静波 <crm8000@qq.com>
// +----------------------------------------------------------------------
namespace Think\Session\Driver;

/**
 * 把session数据存储在数据库里面
 */
class Database implements \SessionHandlerInterface {
	
	/**
	 * Session有效时间
	 */
	private $lifeTime = '';
	
	/**
	 *
	 * @var \mysqli
	 */
	private $db = null;

	public function open($savePath, $sessName) {
		$this->lifeTime = 24 * 60 * 60 * 3; // 三天
		
		$hostName = C('DB_HOST');
		$portNumber = intval(C('DB_PORT'));
		$databaseName = C('DB_NAME');
		$userName = C('DB_USER');
		$password = C('DB_PWD');
		
		$this->db = new \mysqli($hostName, $userName, $password, $databaseName, $portNumber);
		
		return $this->db->connect_errno == null;
	}

	public function close() {
		$this->gc($this->lifeTime);
		
		$this->db->close();
		
		return true;
	}

	public function read($id) {
		$currentTime = time();
		
		$data = "";
		
		$sql = "select session_data
				from think_session 
				where session_id = '{$id}' 
					and session_expire > {$currentTime} ";
		$result = $this->db->query($sql);
		if ($result) {
			$row = $result->fetch_assoc();
			if ($row) {
				$data = $row["session_data"];
			}
			
			$result->close();
		}
		
		return $data;
	}

	public function write($id, $data) {
		$expire = time() + $this->lifeTime;
		
		$sql = "replace into think_session (session_id, session_expire, session_data) 
				values ( '{$id}', {$expire}, '{$data}')";
		$this->db->query($sql);
		
		return true;
	}

	public function destroy($id) {
		$sql = "delete from think_session
				where session_id = '{$id}' ";
		$this->db->query($sql);
		
		return true;
	}

	public function gc($sessMaxLifeTime) {
		$sql = "delete from think_session 
				where session_expire < " . time();
		$this->db->query($sql);
		
		return true;
	}
}