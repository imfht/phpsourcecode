<?php

namespace app\controllers;
use app\helpers\Logger;
use app\helpers\Helper;
use app\manager as mgr;

class Room extends Base{
	
	public function beforeAction() {
		if( empty($this->user->nickname) ) {
			$this->send("errorMsg", "请先设置昵称!");
			return false;
		}
		
		return true;
	}
	
	/**
	 * 创建房间
	 */
	public function actionCreate() {
		$manager = mgr\Room::instance();
		$roomId = $manager->create();
		
		Logger::add($this->user->id . " Create room: ". $roomId);
		
		$users = Helper::getRoomUsersInfo($roomId);
		$this->send('createRoomSuccess', 'success', ['id'=>$roomId, 'users'=>$users]);
	}
	
	/**
	 * 加入房间
	 */
	public function actionJoin() {
		$roomId = trim($this->params['id']);
		
		$room = mgr\Room::instance()->get($roomId);

		if( $room && $room->id ) {
			Logger::add($this->user->id . " Join room: ". $room->id);
			
			$room->broadcast('broadcastUserJoin', 'success', ['uid'=>$this->user->id, 'nickname'=>$this->user->nickname], [$this->user->id]);
			
			$this->user->joinRoom($room->id);
			$room->addUser($this->user->id);
			
			// 返回房间内的用户数据
			$users = Helper::getRoomUsersInfo($room->id);
			$this->send('joinRoom', 'success', ['users'=>$users]);
		} else {
			$this->send('errorMsg', '房间号错误！');
		}
	}
	
	/**
	 * 退出房间
	 */
	public function actionLeave() {
		$room = $this->getRoom();
		$this->user->leaveRoom();
		
		$room && $room->delUser($this->user->id);
		
		$this->send('leaveRoom', 'success');
	}
}