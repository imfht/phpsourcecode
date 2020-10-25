<?php

namespace app\helpers;

class Helper{
	
	protected static $n = 0;
	
	/**
	 * 获取一个不重复的 key
	 */
	public static function generateKey($prefix = null) {
		$num = ++self::$n + 100000;
		return $prefix === null ? $num : $prefix . $num;
	}
	
	/**
	 * 获取某房间内的用列表数据
	 * @param Boolean $isReady 是否只获取已准备的用户
	 */
	public static function getRoomUsersInfo($roomId, $isReady = false) {
		$room = \app\manager\Room::instance()->get($roomId);
		
		if(!$room) return [];
		
		$userManager = \app\manager\User::instance();
		$list = [];
		
		$users = $isReady ? $room->readyUsers : $room->users;
		
		foreach($users as $uid => $item) {
			$user = $userManager->get($uid);
			$user && $list[$uid] = [
				'nickname'=> $user->nickname,
				'score'=> $user->score,
				'isReady'=> $isReady ? true : isset($room->readyUsers[$uid]),
				'isOwner'=> $room->owner->id == $uid
			];
		}
		
		return $list;
	}
	
	/**
	 * 获取某个用户的信息
	 */
	public static function getUserInfo($uid) {
		$userManager = \app\manager\User::instance();
		$user = $userManager->get($uid);
		
		return $user ? ['uid'=>$uid, 'nickname'=>$user->nickname, 'score'=>$user->score] : [];
	}
}

    