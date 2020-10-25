<?php
/**
 * 房间管理器
 */

namespace app\manager;
use app\helpers\Helper;

class Room extends Base{
	
	protected $rooms = []; // 所有房间列表
	
	public static function name() {
		return __CLASS__;
	}
	
	/**
	 * 获取当前用户的房间
	 */
	public function getCurrent() {
		$user = User::instance()->getCurrent();
		
		return $this->get($user->roomId);
	}
	
	/**
	 * 根据房间号获取房间
	 */
	public function get($id) {
		return @ $this->rooms[$id];
	}
	
	/**
	 * 创建房间
	 */
	public function create() {
		$id = Helper::generateKey('ROOM');
		
		$this->rooms[$id] = new \app\libs\Room($id);
		$user = User::instance()->getCurrent();
		$user->roomId = $id;
		
		$this->rooms[$id]->addUser($user->id);
		$this->rooms[$id]->ready();
		
		return $id;
	}
	
	/**
	 * 运行房间任务
	 */
	public function runTask() {
		foreach($this->rooms as $id => $room) {
			if($room->id == null) {
				unset($this->rooms[$id]);
			} else {
				$room->task();
			}
		}
	}
}