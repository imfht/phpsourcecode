<?php
class CRedisQueueWorker extends CRedisQueueBases{
	/**
	 * Reids 分库参数，根据该参数判断该子进程处理的数据
	 */
	protected $index;
	
	public function __construct($args) {
		parent::__construct($args, 'worker');
		
		$this->index = $this->args['index'];
	}

	/**
	 * 运行子进程
	 */
	public function run() {
		$this->logger->worker(sprintf("Time: %s, Worker [%d] runing. Pid: %d", date('H:i:s'), $this->index, posix_getpid()));
		
		$worker = new $this->config->WORKER($this->logger);
		$worker->setShare($this->index);
		
		$signal = $this->signal;
		
		for($count = 0; $count < $this->config->MAX_TASK; $count++) {
			// 是否有停止的信号
			if($signal->dispatch() == $signal::SIGNAL_STOP) {
				$this->logger->worker("Stop...");
				break;
			}
			
			$data = $worker->pop();
			if( isset($data[1]) ) {
				$this->logger->worker("Save data...");
				$worker->setData($data[1])->run();
			} else {
				$this->logger->worker("No data...");
			}
		}
	}
}