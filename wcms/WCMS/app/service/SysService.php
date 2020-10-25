<?php
/**
 * 
 * 系统配置
 * @author ldm
 *
 */
class SysService {
	
	public function getConfig() {
		$rs = SysModel::instance ()->getAllConfig ();
		$config = array ();
		foreach ( $rs as $v ) {
			$config [$v ['name']] = $v ['value'];
		}
		return $config;
	}
	
	public function batchSave($data) {
		
		if (empty ( $data )) {
			return array ('status' => false, 'message' => "更新失败" );
		}
		
		foreach ( $data as $k => $v ) {
			$v = urldecode ( $v );
			$this->saveConfigByName ( $v, $k );
		}
		return array ('status' => true, 'message' => "更改成功" );
	}
	
	public function saveConfigByName($value, $name) {
		return SysModel::instance ()->saveConfigByName ( array ('value' => $value ), array ('name' => $name ) );
	}
	
	public function getNodesByFid($fid) {
		return NodeModel::instance ()->getNodeByFid ( $fid );
	}
}

class SysModel extends Db {
	
	private $_config = 'w_config';
	public function getAllConfig() {
		return $this->getAll ( $this->_config );
	}
	
	public function saveConfigByName($v, $where) {
		return $this->update ( $this->_config, $v, $where );
	}
	
	/**
	 * 
	 * @return SysModel
	 */
	public static function instance() {
		return parent::_instance ( __CLASS__ );
	}
}

class NodeModel extends Db {
	protected $_node = 'w_node';
	/**
	 * 获取权限
	 * @param int $fid  父类
	 * @return array $result
	 */
	public function getNodeByFid($fid) {
		
		return $this->getAll ( $this->_node, array ('fid' => $fid ),null,'sort ASC' );
	}
	
	/**
	 * 返回NodeModel
	 * @return NodeModel
	 */
	public static function instance() {
		return parent::_instance ( __CLASS__ );
	}
}