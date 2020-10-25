<?php
/**
 * 命令行运行
 */
class QueueCommands {
	// @see QueueLoger
	protected $store;
	
	// @see QueueConfig
	protected $config;
	
	public function __construct() {
		$this->config = QueueConfig::instance();
		$this->store = new $this->config->STORE();
		$this->store->setKey($this->config->STORE_MASTER_PID_KEY);
	}
	
	/**
	 * 运行master队列
	 */
	public function runMaster() {
		$queue = new CRedisQueueMaster($_SERVER['argv']);
		$queue->run();
	}
	
	/**
	 * 运行worker
	 */
	public function runWorker($index = 1) {
		$args = array();
		$args['index'] = $index;
		$worker = new CRedisQueueWorker($args);
		$worker->run();
	}
	
	/**
	 * 获取正在运行的 master 进程的ID
	 */
	public function getMasterPid() {
		$pid = $this->store->get();
		$pid = intval($pid);
		
		return $pid;
	}
	
	/**
	 * 重启
	 */
	public function restart() {
		$this->stop();
		//sleep(5);
		$this->start();
	}
	
	/**
	 * 停止
	 */
	public function stop() {
		$pid = $this->getMasterPid();
		
		echo "Wait stop: [$pid] \n";
		posix_kill($pid, SIGTERM);
		
		$n = 0;
		while(++$n < 100) {
			pcntl_waitpid($pid, $status);
			$priority = @ pcntl_getpriority($pid);
			if(false === $priority) break;
			
			echo '.';
			usleep(1000*200);
		}
		
		$this->store->delete();
		echo "Stopped.\n";
	}
	
	/**
	 * 启动, nohup 方式开启master程序
	 */
	public function start() {
		$pid = $this->getMasterPid();
		if( !$pid ) {
			echo "Open master. \n";
			$commands = "nohup {$this->config->START_COMMAND} >> /dev/null 2>&1 &";
			shell_exec($commands);
		}
		echo "Already started.\n";
	}
}