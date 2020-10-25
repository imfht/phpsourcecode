<?php
/**
 * 基类
 * 
 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
 * @package package_name 
 * @version 1.0
 * @copyright xiami
 * @since 2014-5-26 上午10:35:38
 */
class MY_Model extends CI_Model {
	
	/**
	 * db类
	 *
	 * @var DB
	 */
	protected $_db;
	
	/**
	 * 创建时间字段
	 *
	 * @var array
	 */
	protected $_create_time_fields = array('gmt_create', 'create_time');
	
	/**
	 * 更新时间字段
	 *
	 * @var array
	 */
	protected $_update_time_fields = array('gmt_update', 'update_time');
	
	public function __construct() {
		parent::__construct();
		
		$class_name = get_class($this);
		if ($class_name != 'MY_Model') {
			$class_name = strtolower($class_name);
			$class_name = str_replace('_model', '', $class_name);
			$this->_db = $this->load->database($class_name, true);
		}
	}
	
	/**
	 * 魔术方法
	 * 
	 * @param string $method_name
	 * @param array $params
	 * @since 2014-5-26 上午11:20:58
	 */
	public function __call($method_name, $params) {
		if (method_exists($this->_db, $method_name)) {
			return call_user_func_array(array($this->_db, $method_name), $params);
		} else {
			$method_name = explode('_', $method_name);
			$method = '_' . $method_name[0];
			unset($method_name[0]);
			$table = implode('_', $method_name);
			if ($table) {
				if (method_exists($this, $method)) {
					array_unshift($params, $table);
					return call_user_func_array(array($this, $method), $params);
				}
			}
		}
	}
	
	/**
	 * 插入数据
	 *
	 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
	 * @param string $table
	 * @param array $data
	 * @return int
	 * @since 2014-5-26 上午11:21:59
	 */
	protected function _insert($table, $data) {
		if (!$data || !is_array($data)) {
			return 0;
		}
		
		$data = $this->_fill_time_fields($table, $data, true);
		$this->_db->insert($table, $data);
		
		return $this->_db->insert_id();
	}
	
	/**
	 * 更新
	 *
	 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
	 * @param string $table
	 * @param int $id
	 * @param array $data
	 * @param array $where
	 * @since 2014-5-26 下午1:32:45
	 */
	protected function _update($table, $id, $data, $where = array()) {
		if (!$data || !is_array($data) || !($id || $where)) {
			return 0;
		}
		
		if ($id) {
			$id = intval($id);
			if ($id < 1) {
				return 0;
			}
			
			$where = array();
			$where['id'] = $id;
		}
		$data = $this->_fill_time_fields($table, $data);
		$this->_db->update($table, $data, $where);
		
		return $this->_db->affected_rows();
	}
	
	/**
	 * Enter description here ...
	 *
	 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
	 * @param string $table
	 * @param string $id
	 * @param array $where
	 * @param bool $logic_delete
	 * @return int
	 * @since 2014-5-26 下午1:49:09
	 */
	protected function _delete($table, $id, $where=array(), $logic_delete = true) {
		if (!$id && !$where) {
			return 0;
		}
		if ($id) {
			$id = intval($id);
			if ($id < 1) {
				return 0;
			}
				
			$where = array();
			$where['id'] = $id;
		}
		if ($logic_delete) {
			$data['is_delete'] = 1;
			return $this->_update($table, 0, $data, $where);
		} else {
			$this->_db->delete($table, $where);
			return $this->_db->affected_rows();
		}
	}
	
	/**
	 * 获取一条数据
	 *
	 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
	 * @param string $table
	 * @param int $id
	 * @since 2014-5-26 下午1:56:07
	 */
	protected function _getone($table, $id) {
		$id = intval($id);
		if ($id < 1) {
			return array();
		}
		
		$where = array();
		$where['id'] = $id;
		$where['is_delete'] = 0;

		return $this->_db->get_where($table, $where)->row_array();
	}
	
	/**
	 * 获取所有结果集
	 *
	 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
	 * @param string $table
	 * @param array $where
	 * @param string $order
	 * @return array
	 * @since 2014-5-26 下午2:03:33
	 */
	protected function _getall($table, $where = array(), $order = 'id desc') {
		if (!is_array($where)) {
			return array();
		}
		
		if (!isset($where['is_delete'])) {
			$where['is_delete'] = 0;
		}
		
		return $this->_db->order_by($order)->get_where($table, $where)->result_array();
	}
	
	protected function _get($table, $where = array(), $page = 1, $page_size = 20, $order = 'id desc') {
		$result = array(
			'data' => array(),
			'page_info' => array(
				'page' => $page,
				'page_size' => $page_size,
				'total_page' => 0,
				'total_count' => 0,
			)
		);
		if (!is_array($where) || $page < 1 || $page_size < 1) {
			return $result;
		}
		
		if (!isset($where['is_delete'])) {
			$where['is_delete'] = 0;
		}
		$result['data'] = $this->_db->order_by($order)->get_where($table, $where, $page_size, ($page - 1) * $page_size)->result_array();
		$result['page_info']['total_count'] = $this->_db->where($where)->count_all_results($table);
		$result['page_info']['total_page'] = ceil($result['page_info']['total_count'] / $page_size);
		
		return $result;
	}
	
	/**
	 * 将时间字段自动加入
	 *
	 * @author lvlin.ll <lvlin.ll@alibaba-inc.com> 
	 * @param string $table
	 * @param array $data
	 * @param bool $is_update
	 * @since 2014-5-26 上午11:31:11
	 */
	private function _fill_time_fields($table, $data, $is_create = false) {
		$fields = $this->_db->field_data($table);
		foreach ($fields as $item) {
			if ($is_create) {
				if (in_array($item->name, $this->_create_time_fields) && !isset($data[$item->name])) {
					if ($item->type == 'int') {
						$data[$item->name] = time();
					} elseif ($item->type == 'datetime') {
						$data[$item->name] = date('Y-m-d H:i:s');
					} else {
						//empty
					}
				}
			}
			if (in_array($item->name, $this->_update_time_fields) && !isset($data[$item->name])) {
			if ($item->type == 'int') {
						$data[$item->name] = time();
					} elseif ($item->type == 'datetime') {
						$data[$item->name] = date('Y-m-d H:i:s');
					} else {
						//empty
					}
			}
		}
		
		return $data;
	}
}