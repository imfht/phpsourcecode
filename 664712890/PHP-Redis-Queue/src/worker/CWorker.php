<?php
/**
 * 工作进程
 */
abstract class CWorker{
	protected $logger;
	
	protected $data;
	
	/**
	 * redis 链接
	 */
	protected $redis = null;
	
	/**
	 * 分片值，更具该值寻找redis服务器等操作
	 */
	protected $share = null;
	
	public function __construct($logger) {
		$this->logger = $logger;
	}
	
	/**
	 * 初始化一些数据， 该方法在 setData时自动调用，
	 * 需要个性化处理时  子类自行重置该方法即可
	 */
	protected function initData() {
		
	}
	
	/**
	 * 设置数据
	 */
	public function setData($data) {
		$this->data = $data;
		$this->initData();
		return $this;
	}
	
	public function run() {
		$this->logger->worker("Type: ".$this->data['type']);
		$res = false;
		switch($this->data['type']) {
			case 'save':
				!$this->data['new'] && $res = $this->updateRecord();
				(false === $res) && $res = $this->insertRecord();
				break;
			case 'delete':
				$res = $this->deleteRecord();
				break;
			default:
				break;
		}
		
		(false === $res) && $res = array(false, 'Invalid Data!');
		$res[0] ? $this->logger->success($this->data) : $this->logger->failed($this->data, $res[1]);
	}
	
	/**
	 * 设置 分片值
	 */
	public function setShare($value) {
		$this->share = $value;
	}
	
	/**
	 * 删除记录
	 * return Array, array(result, message)
	 */
	abstract protected function deleteRecord();	
	
	/**
	 * 更新记录
	 * return Array, array(result, message)
	 */
	abstract protected function updateRecord();
	
	/**
	 * 插入记录
	 * return Array, array(result, message)
	 */
	abstract protected function insertRecord();
	
	/**
	 * 获取redis链接
	 */
	abstract protected function getRedisConnection();
	
	/**
	 * 弹出数据
	 */
	abstract protected function pop($key = null, $blockTime = 1);
}