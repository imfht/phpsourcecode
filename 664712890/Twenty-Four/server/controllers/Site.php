<?php

namespace app\controllers;
use app\libs\Socket;

class Site extends Base{
	
	/**
	 * 用以保持会话
	 */
	public function actionPing() {
		$this->send('ping', 'ok');
	}
	
	/**
	 * 客户端主动退出
	 */
	public function actionOut() {
		Socket::del($this->user->id);
	}
}