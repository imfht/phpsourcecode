<?php

namespace app\libs;

class User{
	public $id;
	
	public $sock;
	
	public $isHandshake = false;
	
	public $score = 0;
	
	public $nickname = '';
	
	public $roomId = 0;
	
	public function __construct($key, $sock) {
		$this->id = $key;
		$this->sock = $sock;
	}
	
	/**
	 * 握手
	 */
	public function handshake($buffer) {
		if($this->isHandshake) return true;
		
		$this->isHandshake = true;
		Socket::handshake($this->sock, $buffer);
		
		return false;
	}
	
	/**
	 * 加入某房间
	 * @param String $roomId 房间ID
	 */
	public function joinRoom($roomId) {
		$this->roomId = $roomId;
	}
	
	/**
	 * 退出房间
	 */
	public function leaveRoom() {
		$this->roomId = 0;
	}
	
	/**
	 * 准备游戏
	 */
	public function ready() {
		$this->score = 0;
		
	}
}