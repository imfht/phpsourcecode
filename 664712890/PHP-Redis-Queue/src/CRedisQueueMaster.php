<?php
class CRedisQueueMaster extends CRedisQueueBases{
	/**
	 * 保存子进程的 PID 列表
	 */
	protected $childs = array();
	
	public function __construct($args) {
		parent::__construct($args, 'master');
		
		for($i = 1; $i <= $this->config->QUEUE_COUNT; $i++) {
			$this->childs[$i] = 0;
		}
		
		$pid = posix_getpid();
		$this->store->setKey($this->config->STORE_MASTER_PID_KEY)->set($pid);
	}
	
	/**
	 * 运行主进程
	 * 监控子进程，并启动子进程
	 */
	public function run() {
		$signal = $this->signal;
		
		while(true) {
			foreach($this->childs as $index => $pid) {
				// 是否有停止的信号
				if($signal->dispatch() == $signal::SIGNAL_STOP) {
					$this->logger->master("Stop...\n");
					$this->stop();
					exit();
				}
				
				$pid && $res = pcntl_waitpid($pid, $status, WNOHANG);
				if(!$pid || $res == $this->config->FINISH_STATE)
					$this->forkWorker($index);
			}
			
			$this->config->load();
			sleep($this->config->INTERVAL);
		}
	}
	
	/**
	 * 向子进程发送终止信号
	 * 然后等待子进程结束，防止产生僵尸进程
	 */
	protected function stop() {
		$this->logger->master("Stopping...\n");
		
		foreach($this->childs as $pid) {
			posix_kill($pid, SIGTERM);
		}
		
		while(count($this->childs) > 0) {
			foreach($this->childs as $index => $pid) {
				$res = pcntl_waitpid($pid, $status, WNOHANG);
				
				if($res == -1 || $res == $pid) {
					unset($this->childs[$index]);
					
					$this->logger->master(sprintf("Child [%d -> %d] stopped.\n", $index, $pid));
				}
			}
			sleep(1);
		}
		
		$this->logger->master("Finish stop.\n");
	}
	
	/**
	 * 开启子进程
	 * @params Int $index 子进程参数
	 */
	protected function forkWorker($index) {
		$log = sprintf("Start fork! Index: %d\n", $index);
		$this->logger->master($log);
		
		// 开启分支
		$pid = pcntl_fork();
		if($pid == -1) {
			$log = sprintf("Fork worker failed! Index: %d\n", $index);
			return $this->logger->master($log);
		}
		
		if($pid) {
			$log = sprintf("Fork worker success! pid: %s!\n", $pid);
			$this->logger->master($log);
			$this->childs[$index] = $pid;
		} else {
			$args = $this->args;
			$args['index'] = $index;
			$worker = new CRedisQueueWorker($args);
			$worker->run();
			
			// 此处中断子进程
			exit();
		}
		
		$log = sprintf("Finish fork! Index: %d\n", $index);
		$this->logger->master($log);
	}
}