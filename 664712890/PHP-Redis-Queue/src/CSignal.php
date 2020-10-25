<?php
/**
 * 队列进程信号绑定
 */

class CSignal{
	/**
	 * 实例化我的对象
	 */
	protected $caller;
	
	/**
	 * 场景， master|worker
	 */
	protected $scene;
	
	/**
	 * 信号状态
	 */
	protected $state = 0;
	
	const SIGNAL_STOP = -1; // 停止队列
	const SIGNAL_WAIT = -2; // 等待队列
	const SIGNAL_NULL = -99; // 其它
	const SIGNAL_START = 1; // 开始队列
	const SIGNAL_RUNNING = 2; // 正在运行
	
	public function __construct($caller, $scene) {
		$this->caller = $caller;
		$this->scene = $scene;
		$this->state = self::SIGNAL_RUNNING;
		
		$this->bindingSignals();
	}
	
	/**
	 * 触发信号
	 */
	public function dispatch() {
		pcntl_signal_dispatch();
		return $this->state;
	}
	
	/**
	 * 绑定信号
	 */
	protected function bindingSignals() {
		// 重新开始信号
		pcntl_signal(SIGUSR1, array($this, 'sigHandler'));
		
		// 终止信号
		pcntl_signal(SIGTERM, array($this, 'sigHandler'));
		
		// 挂起，等待信号
		pcntl_signal(SIGHUP, array($this, 'sigHandler'));
	}
	
	/**
	 * 信号处理句柄
	 */
	public function sigHandler($signo) {
		$signal = '';
		switch($signo) {
			case SIGUSR1:
				$this->state = self::SIGNAL_RUNNING;
				$signal = 'SIGNAL_RUNNING';
				break;
			case SIGTERM:
				$this->state = self::SIGNAL_STOP;
				$signal = 'SIGNAL_STOP';
				break;
			case SIGHUP:
				$this->state = self::SIGNAL_WAIT;
				$signal = 'SIGNAL_WAIT';
				break;
			default: 
				$this->state = self::SIGNAL_NULL;
				$signal = 'SIGNAL_NULL';
				break;
		}
		
		$this->caller->getLogger()->{$this->scene}(sprintf("Signal: %s\n", $signal));
	}
}
