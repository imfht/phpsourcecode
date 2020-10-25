<?php
/**
 * 队列管理
 * php yiic.php start
 */

ini_set('default_socket_timeout', -1);
set_time_limit(0);
error_reporting(E_ALL);

class QueueCommand extends CConsoleCommand {
	protected $commands;
	
	public function init(){
		Yii::import("system.common.queue_v2.*");
		Yii::import("system.common.queue_v2.store.*");
		Yii::import("system.common.queue_v2.worker.*");
		
		$this->commands = new QueueCommands;
		CRedisConfig::instance()->timeOut = 20;
	}

	public function actionMaster() {
		$this->commands->runMaster();
	}
	
	/**
	 * 开启一个 worker, 
	 */
	public function actionWorker($index = 1) {
		$this->commands->runWorker($index);
	}

	/**
	 * 开启队列
	 */
	public function actionStart() {
		$this->commands->start();
	}
	
	/**
	 * 停止队列
	 */
	public function actionStop() {
		$this->commands->stop();
	}
	
	/**
	 * 重启队列
	 */
	public function actionRestart() {
		$this->commands->restart();
	}
}