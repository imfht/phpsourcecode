<?php
/**
 * 队列日志记录器
 */
class QueueLoger{
	/**
	 * 按月划分子目录
	 */
	protected $baseDir = '';
	
	/**
	 * 日志文件句柄
	 * 分类存储日志，成功、失败、运行时日志等
	 */
	protected $fps = null;
	
	/**
	 * 当前的 日志句柄
	 */
	protected $fp;
	
	/**
	 * worker 的运行时日志 先写到内存，当worker退出时在写到 文件
	 * 避免 多个worker日志 混乱
	 */
	protected $workerLogs = '';
	
	/**
	 * 今天 date('Ymd')
	 */
	protected $day = '';
	
	public function __construct() {
		$config = QueueConfig::instance();
		
		$this->baseDir = sprintf("%s/%s/", $config->LOG_DIR, date('Ym'));
		!is_dir($this->baseDir) && mkdir($this->baseDir, 777, true);
		
		$this->day = date('Ymd');
		$this->fps = new stdClass;
	}
	
	public function __call($method, $args) {
		$callMethod = 'log'.ucfirst($method);
		if(!method_exists($this, $callMethod))
			throw new CException(sprintf("Method is not exists: %s:%s", __CLASS__, $method));
		
		$key = in_array($method, array('failed', 'success')) && isset($args[0]['table']) 
			? $method.'_'.$args[0]['table'] : $method;
		
		if(!isset($this->fps->{$key})) {
			$file = sprintf('%s_%s.log', $key, $this->day);
			$this->fps->{$key} = fopen($this->baseDir.$file, 'a+');
		}
		
		$this->fp = $this->fps->{$key};
		return call_user_func_array(array($this, $callMethod), $args);
	}
	
	/**
	 * 记录保存失败的日志
	 */
	protected function logFailed($data, $errors) {
		$log = sprintf("\n Time: %s\n, %s\n, Errors: %s\n", 
			date('H:i:s'), @ var_export($data, true), var_export($errors, true));
		
		$this->save($log);
	}
	
	/**
	 * 记录保存成功的日志
	 */
	protected function logSuccess($data) {
		$log = sprintf("\n Time: %s\n, %s\n", 
			date('H:i:s'), @ var_export($data, true));
		
		$this->save($log);
	}
	
	/**
	 * 记录 worker 运行时日志
	 */
	protected function logWorker($string) {
		$this->workerLogs .= $string."\n";
	}
	
	/**
	 * 记录master 运行日志
	 */
	protected function logMaster($string) {
		$string = sprintf("Time: %s, %s\n", date('Y-m-d H:i:s'), $string);
		$this->save($string);
	}
	
	/**
	 * 写入记录，对文件加锁
	 */
	protected function save($log) {
		flock($this->fp, LOCK_EX);
		fwrite($this->fp, $log);
		flock($this->fp, LOCK_UN);
	}

	public function __destruct() {
		if(!empty($this->workerLogs)) {
			$this->fp = $this->fps->worker;
			$this->save("\n".$this->workerLogs);
		}
		
		foreach($this->fps as $fp) {
			@flock($fp, LOCK_UN);
			fclose($fp);
		}
	}
}