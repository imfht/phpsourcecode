<?php
/**
 * 队列配置管理器
 */
class QueueConfig{
	private $_config = array();
	
	public static $configPath = null;
	
	private static $_instance = null;
	
	public static function instance() {
		if(null === self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function __construct() {
		$this->load();
	}
	
	/**
	 * 载入配置文件
	 */
	public function load() {
		$config = $this->getConfigPath();
		$this->_config = include $config;
	}
	
	/**
	 * 获取配置文件路径
	 */
	public function getConfigPath() {
		$config = self::$configPath ? self::$configPath : 
			(class_exists('Yii', false) ? Yii::app()->params->queueConfig : null);
		
		if(null === $config) {
			if( class_exists('Yii', false) )
				throw new CException("Please set config path, at 'Yii::app()->params->queueConfig'!");
			else
				throw new CException('Please set config path, use: QueueConfig::$configPath=...');
		}
		
		if(!is_file($config))
			throw new CException("Invalid configuration file: ".$config);
			
		return $config;
	}
	
	public function __get($name) {
		return isset($this->_config[$name]) ? $this->_config[$name] : '';
	}
}