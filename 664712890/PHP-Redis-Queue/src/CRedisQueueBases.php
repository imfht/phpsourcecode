<?php
class CRedisQueueBases{
	/**
	 * 日志记录器
	 */
	protected $logger;
	
	/**
	 * 配置文件
	 */
	protected $config = array();
	
	/**
	 * 存储工具
	 */
	protected $store;
	
	/**
	 * 信号处理器
	 */
	protected $signal;
	
	/**
	 * 参数
	 */
	protected $args = array();
	
	public function __construct($args, $scene = 'master') {
		$this->args = $args;
		$this->config = QueueConfig::instance();
		$this->logger = new QueueLoger();
		$this->store = new $this->config->STORE();
		
		$this->signal = new CSignal($this, $scene);
	}
	
	public function getLogger() {
		return $this->logger;
	}
	
	public function getConfig() {
		return $this->config;
	}
	
	public function getStore() {
		return $this->store;
	}
}