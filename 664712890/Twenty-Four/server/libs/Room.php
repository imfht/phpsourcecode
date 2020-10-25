<?php

namespace app\libs;
use app\helpers\Logger;
use app\helpers\Helper;
use \app\manager as mgr;

class Room{
	public $id; // 房间ID
	
	public $owner; // 房主
	
	public $users = []; // 进入房间的用户
	
	public $readyUsers = []; // 准备中的/游戏中 的用户
	
	public $userCount = 0;
	
	public $readyUserCount = 0;
	
	public $ingame = false; // 游戏是否进行中
	
	public $canAnswer = false; // 是否可以答题
	
	public $sendQuestion = false; // 是否发送题目，
	
	public $lastTime = 0; // 上一次出题时间
	
	public $interval = 10; // 出题间隔时间
	
	public $limit = [1, 10]; // 人数限制
	
	public $question = []; // 当前题目
	
	public $answeredNumber = 0; // 已答题数
	
	public $maxQuestionNumber = 10; // 每次游戏的最大题目数量
	
	public $isPoints = false; // 答错是否扣分
	
	public function __construct($id) {
		$this->id = $id;
		$this->owner = mgr\User::instance()->getCurrent();
		$this->users[$this->owner->id] = 1;
	}
	
	/**
	 * 用户准备
	 */
	public function ready() {
		if($this->ingame) return [false, '无法加入，游戏已开始!'];
		
		$user = mgr\User::instance()->getCurrent();
		
		if( $this->readyUserCount >= $this->limit[1] ) return [false, '房间已满员!试一试创建房间吧。']; // 满员
		
		$this->readyUsers[$user->id] = 1;
		$this->readyUserCount = count($this->readyUsers);
		
		return [true, 'success'];
	}
	
	/**
	 * 开始游戏
	 */
	public function startGame($questionNumber, $isPoints) {
		if($this->ingame) return [false, '游戏已开始!'];
		
		Logger::add("Ready user number: " . $this->readyUserCount);
		
		if($this->readyUserCount < $this->limit[0]) {
			return [false, '人数不足!邀请朋友一起来玩.'];
		}
		
		$this->maxQuestionNumber = $questionNumber;
		$this->isPoints = $isPoints;
		$this->answeredNumber = 0;
		$this->ingame = true;
		$this->nextQuestion();
		
		return [true, 'success'];
	}
	
	/**
	 * 结束游戏
	 * 取消所有玩家的准备状态
	 */
	public function finishGame() {
		$this->ingame = false;
		$users = Helper::getRoomUsersInfo($this->id, true);
		
		$this->readyUsers = [$this->owner->id=>1];
		$this->readyUserCount = 1;
		
		$this->broadcast('finishGame', 'success', $users);
	}
	
	/**
	 * 开始准备下一题
	 */
	public function nextQuestion() {
		// 达到最大 答题数，游戏结束
		if($this->answeredNumber >= $this->maxQuestionNumber) {
			$this->finishGame();
			return false;
		}
		
		$this->answeredNumber++;
		$this->canAnswer = false;
		$this->sendQuestion = true;
		$this->lastTime = time();
		$this->interval = 5;
		
		$this->broadcast('broadcastNextQuestion', 'success');
		return true;
	}
	
	/**
	 * 删除房间内的某用户
	 */
	public function delUser($uid) {
		unset($this->users[$uid]);
		unset($this->readyUsers[$uid]);
		$this->userCount = count($this->users);
		$this->readyUserCount = count($this->readyUsers);
		
		$user = mgr\User::instance()->get($uid);
		$this->broadcast('broadcastUserLeave', 'success', ['uid'=>$uid, 'nickname'=>$user->nickname]);
		
		// 房主退出
		if($uid == $this->owner->id) {
			if( $this->readyUserCount > 0) {
				
				foreach($this->readyUsers as $uid => $n) {
					$this->changeOwner($uid);
					break;
				}
				
			} else if($this->userCount > 0) {
				
				foreach($this->users as $uid => $n) {
					$this->changeOwner($uid);
					break;
				}
				
			} else {
				// 没有其他人了，房间自动解散
				$this->dissolve();
			}
		}
	}
	
	/**
	 * 房主变更
	 */
	public function changeOwner($uid) {
		$userManager = mgr\User::instance();
		$this->owner = $userManager->get($uid);
		
		Socket::send($uid, 'toRoomOwner', 'success');
		$this->broadcast('broadcastChangeOwner', 'success', ['uid'=>$uid, 'nickname'=>$this->owner->nickname], [$uid]);
	}
	
	/**
	 * 添加用户
	 */
	public function addUser($uid) {
		$this->users[$uid] = 1;
		$this->userCount = count($this->users);
	}
	
	/**
	 * 解散
	 */
	public function dissolve() {
		$this->ingame = false;
		// 将ID 设置为null， 等待 roomManager 回收
		$this->id = null;
	}
	
	/**
	 * 任务
	 */
	public function task() {
		if($this->ingame && $this->sendQuestion && ($this->lastTime+$this->interval) < time()) {
			$this->sendQuestion = false;
			$this->question = Question::create();
			$this->canAnswer = true;
			
			Logger::add('Questin solution:' . $this->question[1]);
			// 发放题目
			$this->broadcast('question', '开始抢答。', $this->question[0]);
		}
	}
	
	/**
	 * 获取当前题目
	 */
	public function getQuestion() {
		return $this->question;
	}
	
	/**
	 * 广播，对房间内的人进行广播
	 */
	public function broadcast($type, $msg, $data = [], $exceptions = []) {
		foreach($this->users as $uid => $n) {
			if( empty($exceptions) || !in_array($uid, $exceptions, true) ) {
				Socket::send($uid, $type, $msg, $data);
			}
		}
	}
	
}