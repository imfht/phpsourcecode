<?php

namespace app\controllers;

class User extends Base{
	
	/**
	 * 修改昵称
	 */
	public function actionNickname() {
		$nickname = trim($this->params['value']);
		
		if( empty($nickname) ) return $this->send('errorMsg', '昵称不能为空!');
		if( mb_strlen($nickname) > 8 ) return $this->send('errorMsg', '昵称最多8个字符!');
		
		$this->user->nickname = htmlspecialchars($nickname, ENT_QUOTES);
	
		$this->send('nicknameSetSuccess', '昵称设置成功', ['nickname'=>$this->user->nickname, 'uid'=>$this->user->id]);
	}
}