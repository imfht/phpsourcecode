<?php
/**
 * 用户管理器
 */

namespace app\manager;
use app\helpers\Helper;
use app\helpers\Logger;

class User extends Base{

	protected $users = []; // 所有用户列表
	
	private $_current = null;
	
	public static function name() {
		return __CLASS__;
	}
	
	/**
	 * 设置当前用户
	 */
	public function setCurrent($sock) {
		$this->_current = null;
		
		foreach($this->users as $user) {
			if($sock == $user->sock) {
				$this->_current = $user;
				break;
			}
		}
		
		return $this->_current;
	}
	
	/**
	 * 获取当前用户
	 */
	public function getCurrent() {
		return $this->_current;
	}

	/**
	 * 创建用户
	 */
	public function create($sock) {
		$uid = Helper::generateKey();
		$this->users[$uid] = new \app\libs\User($uid, $sock);
		return $uid;
	}
	
	/**
	 * 根据 uid 获取用户
	 */
	public function get($uid) {
		return @ $this->users[$uid];
	}
	
	/**
	 * 获取全部用户
	 */
	public function getAll() {
		return $this->users;
	}
	
	/**
	 * 根据uid 删除用户
	 */
	public function del($uid) {
		$roomManager = \app\manager\Room::instance();
		$user = $this->get($uid);
		
		if( $user->roomId ) {
			$room = $roomManager->get($user->roomId);
			$room && $room->delUser($user->id);
		}
		
		unset($this->users[$uid]);
		$user = null;
		
		Logger::add("User: {$uid} closed");
	}
}