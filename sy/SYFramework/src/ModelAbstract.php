<?php
/**
 * Model基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy;

use Sy\DB\DBInterface;
use Sy\Exception\Exception;
use Sy\Exception\DBException;
use Latitude\QueryBuilder\Conditions;

abstract class ModelAbstract {
	protected $_table_name = '';
	protected $_primary_key = 'id';
	protected $driver;
	public function __construct(DBInterface $driver) {
		//检查table_name是否为空
		if (empty($this->_table_name)) {
			throw new Exception('Table name can not be empty');
		}
		$this->driver = $driver;
	}
	/**
	 * 获取Builder实例类
	 * @access public
	 * @return object
	 */
	public function getBuilder() {
		return get_class($this->driver)::getBuilder();
	}
	public function newSelect() {
		return $this->getBuilder()->select()->from($this->_table_name);
	}
	public function newInsert() {
		return $this->getBuilder()->insert($this->_table_name);
	}
	public function newUpdate() {
		return $this->getBuilder()->update($this->_table_name, []);
	}
	public function newDelete() {
		return $this->getBuilder()->delete($this->_table_name);
	}
	/**
	 * 执行一条Builder的结果
	 * @access public
	 * @param object $builder
	 * @return array
	 */
	public function execute($query) {
		return $this->driver->query($query->sql(), $query->params());
	}
	/**
	 * 查询一条数据
	 * @access public
	 * @param mixed $filter 当$filter为array时，则为多条条件，否则为主键
	 * @param array $cols 需要查询出的列
	 */
	public function get($filter, $cols = null) {
		$query = $this->newSelect();
		if (is_array($cols)) {
			$query->columns(...$cols);
		}
		if ($filter !== null) {
			if (!is_array($filter)) {
				$query->where(Conditions::make($this->_primary_key . ' = ?', $filter));
			} else {
				$where = Conditions::make();
				foreach ($filter as $k => $v) {
					$where->andWith($k . ' = ?', $v);
				}
				$query->where($where);
			}
		}
		$query->limit(1);
		$result = $this->execute($query);
		return count($result) > 0 ? current($result) : null;
	}
	/**
	 * 查询多条数据
	 * @access public
	 * @param array $filter
	 * @param int $num
	 * @param int $offset
	 * @param array $cols 需要查询出的列
	 * @return array
	 */
	public function list($filter = [], $num = 30, $offset = 0, $cols = null) {
		$query = $this->newSelect();
		if (is_array($cols)) {
			$query->columns(...$cols);
		}
		if (count($filter) > 0) {
			$where = Conditions::make();
			foreach ($filter as $k => $v) {
				$where->andWith($k . ' = ?', $v);
			}
			$query->where($where);
		}
		$query->offset($offset)->limit($num);
		return $this->execute($query);
	}
	/**
	 * 修改一条或多条数据
	 * 当传入两个参数时，会认为第二个参数是$filter
	 * 注意：$filter不能为空，如果要更新所有数据，必须传入$filter为TRUE
	 * 
	 * @access public
	 * @param array $set
	 * @param array $cols
	 * @param array $filter
	 * @return int
	 */
	public function set($set, $cols, $filter = null) {
		if ($filter === null) {
			$filter = &$cols;
		} else {
			//筛选$set列
			foreach ($set as $k => $v) {
				if (!in_array($k, $cols, true)) {
					unset($set[$k]);
				}
			}
		}
		$query = $this->newUpdate();
		$query->map($set);
		if ($filter !== true) {
			if (is_string($filter) || is_numeric($filter)) {
				$query->where(Conditions::make($this->_primary_key . ' = ?', $filter));
			} elseif (!is_array($filter) || count($filter) === 0) {
				throw new DBException("Filter can not be empty");
			} else {
				$where = Conditions::make();
				foreach ($filter as $k => $v) {
					$where->andWith($k . ' = ?', $v);
				}
				$query->where($where);
			}
		}
		$res = $this->execute($query);
		return intval($res['_affected_rows']);
	}
	/**
	 * 删除数据
	 * 注意：$filter不能为空，如果要清除所有数据，必须传入$filter为TRUE
	 * 
	 * @access public
	 * @param array|string|int|bool $filter
	 */
	public function del($filter) {
		$query = $this->newDelete();
		if ($filter !== true) {
			if (is_string($filter) || is_numeric($filter)) {
				$query->where(Conditions::make($this->_primary_key . ' = ?', $filter));
			} elseif (!is_array($filter) || count($filter) === 0) {
				throw new DBException("Filter can not be empty");
			} else {
				$where = Conditions::make();
				foreach ($filter as $k => $v) {
					$where->andWith($k . ' = ?', $v);
				}
				$query->where($where);
			}
		}
		$res = $this->execute($query);
		return intval($res['_affected_rows']);
	}
	/**
	 * 添加数据
	 * 如果指定了$primary_key，则会返回最后一次生成的ID
	 * 否则返回NULL
	 * 
	 * @access public
	 * @param array $data
	 * @param array $cols
	 * @return int/null
	 */
	public function add(array $data, $cols = null) {
		if (is_array($cols)) {
			//筛选$data列
			foreach ($data as $k => $v) {
				if (!in_array($k, $cols, true)) {
					unset($data[$k]);
				}
			}
		}
		$query = $this->newInsert()->map($data);
		$result = $this->execute($query);
		if (!empty($this->_primary_key)) {
			return intval($result['_insert_id']);
		} else {
			return null;
		}
	}
}
