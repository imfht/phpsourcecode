<?php
/**
 * File 存储
 */
class CFileStore extends CStore{
	protected $storeFile;
	
	public function afterSetKey() {
		$dir = QueueConfig::instance()->STORE_DIR;
		if(!is_dir($dir)) mkdir($dir, 0755, true);
		$this->storeFile = sprintf("%s/%s.cache", $dir, $this->getKey());
	}
	
	public function set($value, $ttl = 0) {
		return @ file_put_contents($this->storeFile, $value);
	}
	
	public function setnx($value, $ttl = 0) {
		return $this->has() ? true : $this->set($value, $ttl);
	}
	
	public function get() {
		return $this->has() ? @ file_get_contents($this->storeFile) : null;
	}
	
	public function delete() {
		return $this->has() ? @ unlink($this->storeFile) : true;
	}
	
	public function has() {
		return is_file($this->storeFile);
	}
}