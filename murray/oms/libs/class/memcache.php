<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package MEMCACHE类
*/

defined('INPOP') or exit('Access Denied');

class memcache{

	private static $_instance;
	private static $_connect_type = '';
	private $_memcache;
 
	//禁止使用关键字new来实例Memcache类
	private function __construct() {
		if (!class_exists('Memcache')) {
			exit('Class Memcache not exists');
		} 
		$func = self::$_connect_type;
		$this->_memcache = new Memcache();
		$this->_memcache->$func(MEMCACHE_HOST, MEMCACHE_PORT);
	}
 
	//克隆私有化，禁止克隆实例
	private function __clone() {}
 
	//实例化
	public static function getInstance($type = 'connect'){
		self::$_connect_type = ($type == 'connect') ? $type : 'pconnect';
		if (!self::$_instance instanceof self) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
 
	//把数据添加到缓存
	public function set($key, $value, $expire_time = 0){
		if ($expire_time > 0) {
			$this->_memcache->set($key, $value, 0, $expire_time);   
		}else{
			$this->_memcache->set($key, $value);  
		} 
	}
 
	//从缓存读取数据
	public function get($key){
		return $this->_memcache->get($key); 
	}
 
	//从缓存删除数据
	public function del($key){
		$this->_memcache->delete($key);  
	}
	
}
?>