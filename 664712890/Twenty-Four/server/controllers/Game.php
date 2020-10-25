<?php

namespace app\controllers;
use app\helpers\Logger;
use app\helpers\Helper;

class Game extends Base{
	
	protected $room;
	
	public function beforeAction() {
		if( empty($this->user->nickname) ) {
			$this->send("errorMsg", "请先设置昵称!");
			return false;
		}
		
		$this->room = \app\manager\Room::instance()->get($this->user->roomId);

		if(!$this->room) {
			$this->send("errorMsg", "请先创建房间或加入房间!");
			return false;
		}
		
		return true;
	}
	
	/**
	 * 准备
	 */
	public function actionReady() {
		Logger::add("User ".$this->user->id." ready");
		
		list($res, $msg) = $this->room->ready();
		
		if($res === true) {
			$this->room->broadcast('broadcastUserReady', 'success', ['uid'=>$this->user->id, 'nickname'=>$this->user->nickname], [$this->user->id]);
			
			$this->user->ready();
			$this->send('readySuccess', 'success');
		} else {
			$this->send('errorMsg', $msg);
		}
	}
	
	/**
	 * 房主开始游戏
	 */
	public function actionStart() {
		if($this->room->owner->id != $this->user->id) {
			return $this->send('errorMsg', '您不是房主!无法开始游戏.');
		}
		
		$this->user->ready();
		
		$questionNumber = intval( $this->params['questionNumber'] );
		$isPoints = intval( $this->params['isPoints'] ) ? true : false;
		
		!in_array($questionNumber, [3,5,10,20], true) && $questionNumber = 5;
		
		list($res, $msg) = $this->room->startGame($questionNumber, $isPoints);
		
		if($res === true) {
			$this->room->broadcast('startGame', 'success', ['questionNumber'=>$questionNumber, 'isPoints'=>$isPoints]);
		} else {
			$this->send('errorMsg', $msg);
		}
	}
	
	/**
	 * 回答问题
	 */
	public function actionAnswer() {
		if( !$this->room->canAnswer ) {
			return $this->send('errorMsg', '尚未开始，休息片刻。');
		}
		
		if( \app\helpers\Game::check($this->params['value']) ) {
			$this->user->score++;
			$this->send('answerResult', 'success', ['result'=>true]);
			
			$this->room->broadcast('answered', 'success', ['nickname'=>$this->user->nickname, 'uid'=>$this->user->id], [$this->user->id]);
			
			$this->room->nextQuestion();
		} else {
			$this->room->isPoints && $this->user->score--;
			$this->send('answerResult', 'success', ['result'=>false]);
		}
	}
	
}