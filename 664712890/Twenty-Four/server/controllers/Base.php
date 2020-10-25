<?php

namespace app\controllers;

class Base{
	
	// 参数
	protected $params;
	
	// 当前用户
	protected $user;
	
	public function __construct($params) {
		$this->user = \app\manager\User::instance()->getCurrent();
		$this->params = $params;
	}
	
	public function beforeAction() {
		
		
		return true;
	}
	
	/**
	 * 给当前用户发送消息
	 */
	protected function send($type, $msg, $data = []) {
		$res = \app\libs\Socket::send($this->user->id, $type, $msg, $data);
		
		return $res;
	}
	
	/**
	 * 获取当前用户所在房间
	 */
	protected function getRoom() {
		return \app\manager\Room::instance()->get($this->user->roomId);
	}
	
	public function afterAction() {
		
	}
}