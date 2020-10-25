<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libsrv;

use tfc\ap\ErrorException;
use tdo\DynamicDb;

/**
 * DynamicService abstract class file
 * 业务层：动态模型基类，自动读取和操作数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DynamicService.php 1 2013-05-18 14:58:59Z huan.song $
 * @package libsrv
 * @since 1.0
 */
abstract class DynamicService extends AbstractService
{
	/**
	 * @var string 缺省的表名与分表数字之间的连接符
	 */
	const DEFAULT_TABLE_NUM_JOIN = '_';

	/**
	 * @var string 表名
	 */
	protected $_tableName = '';

	/**
	 * @var integer 分表数字，如果 >= 0 表示分表操作
	 */
	protected $_tableNum = -1;

	/**
	 * @var string 表名与分表数字之间的连接符
	 */
	protected $_tableNumJoin = self::DEFAULT_TABLE_NUM_JOIN;

	/**
	 * 构造方法：初始化分表数字
	 * @param integer $tableNum 分表数字，如果 >= 0 表示分表操作
	 */
	public function __construct($tableNum = -1)
	{
		parent::__construct();

		$this->_tableNum = (int) $tableNum;
	}

	/**
	 * 通过多个字段名和值，获取主键的值，字段之间用简单的AND连接。不支持联合主键
	 * @param array $attributes
	 * @return mixed
	 */
	public function getPkByAttributes(array $attributes = array())
	{
		$value = $this->getDb()->getPkByAttributes($attributes);
		return $value;
	}

	/**
	 * 通过多个字段名和值，获取某个列的值，字段之间用简单的AND连接，字段之间用简单的AND连接
	 * @param string $columnName
	 * @param array $attributes
	 * @return mixed
	 */
	public function getByAttributes($columnName, array $attributes = array())
	{
		$value = $this->getDb()->getByAttributes($columnName, $attributes);
		return $value;
	}

	/**
	 * 通过多个字段名和值，查询两个字段记录，字段之间用简单的AND连接，并且以键值对方式返回
	 * @param array $columnNames
	 * @param array $attributes
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function findPairsByAttributes(array $columnNames, array $attributes = array(), $order = '', $limit = 0, $offset = 0)
	{
		$rows = $this->getDb()->findPairsByAttributes($columnNames, $attributes, $order, $limit, $offset);
		return $rows;
	}

	/**
	 * 通过多个字段名和值，查询多条记录，字段之间用简单的AND连接，只查询指定的字段
	 * @param array $columnNames
	 * @param array $attributes
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findColumnsByAttributes(array $columnNames, array $attributes = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$condition = $this->getDb()->getCommandBuilder()->createAndCondition(array_keys($attributes));
		return $this->findColumnsByCondition($columnNames, $condition, $attributes, $order, $limit, $offset, $option);
	}

	/**
	 * 通过多个字段名和值，查询多条记录，字段之间用简单的AND连接
	 * @param array $attributes
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAllByAttributes(array $attributes = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$condition = $this->getDb()->getCommandBuilder()->createAndCondition(array_keys($attributes));
		return $this->findAllByCondition($condition, $attributes, $order, $limit, $offset, $option);
	}

	/**
	 * 通过多个字段名和值，统计记录数，字段之间用简单的AND连接
	 * @param array $attributes
	 * @return integer
	 */
	public function countByAttributes(array $attributes = array())
	{
		$total = $this->getDb()->countByAttributes($attributes);
		return $total;
	}

	/**
	 * 通过多个字段名和值，查询一条记录，字段之间用简单的AND连接
	 * @param array $attributes
	 * @return array
	 */
	public function findByAttributes(array $attributes = array())
	{
		$row = $this->getDb()->findByAttributes($attributes);
		return $row;
	}

	/**
	 * 通过多个字段名和值，查询多条记录，字段之间用简单的AND连接，findAllByAttributes别名
	 * @param array $attributes
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $attributes = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		return $this->findAllByAttributes($attributes, $order, $limit, $offset, $option);
	}

	/**
	 * 通过条件，查询两个字段记录，并且以键值对方式返回
	 * @param array $columnNames
	 * @param string $condition
	 * @param mixed $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function findPairsByCondition(array $columnNames, $condition, $params = null, $order = '', $limit = 0, $offset = 0)
	{
		$rows = $this->getDb()->findPairsByCondition($columnNames, $condition, $params, $order, $limit, $offset);
		return $rows;
	}

	/**
	 * 通过条件，查询多条记录，只查询指定的字段
	 * @param string $condition
	 * @param mixed $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findColumnsByCondition(array $columnNames, $condition, $params = null, $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$rows = $this->getDb()->findColumnsByCondition($columnNames, $condition, $params, $order, $limit, $offset, $option);
		if (is_array($rows) && $option === 'SQL_CALC_FOUND_ROWS') {
			$rows['attributes'] = $params;
			$rows['order'] = $order;
			$rows['limit'] = $limit;
			$rows['offset'] = $offset;
		}

		return $rows;
	}

	/**
	 * 通过条件，查询多条记录，如果$option=SQL_CALC_FOUND_ROWS，则不记录缓存，并返回总记录行数
	 * @param string $condition
	 * @param mixed $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAllByCondition($condition, $params = null, $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$rows = $this->getDb()->findAllByCondition($condition, $params, $order, $limit, $offset, $option);
		if (is_array($rows) && $option === 'SQL_CALC_FOUND_ROWS') {
			$rows['attributes'] = $params;
			$rows['order'] = $order;
			$rows['limit'] = $limit;
			$rows['offset'] = $offset;
		}

		return $rows;
	}

	/**
	 * 通过条件，统计记录数
	 * @param string $condition
	 * @param mixed $params
	 * @return integer
	 */
	public function countByCondition($condition, $params = null)
	{
		$total = $this->getDb()->countByCondition($condition, $params);
		return $total;
	}

	/**
	 * 通过条件，获取主键的值。不支持联合主键
	 * @param string $condition
	 * @param mixed $params
	 * @return mixed
	 */
	public function getPkByCondition($condition, $params = null)
	{
		$value = $this->getDb()->getPkByCondition($condition, $params);
		return $value;
	}

	/**
	 * 通过条件，获取某个列的值
	 * @param string $columnName
	 * @param string $condition
	 * @param mixed $params
	 * @return mixed
	 */
	public function getByCondition($columnName, $condition, $params = null)
	{
		$value = $this->getDb()->getByCondition($columnName, $condition, $params);
		return $value;
	}

	/**
	 * 通过条件，查询一条记录
	 * @param string $condition
	 * @param mixed $params
	 * @return array
	 */
	public function findByCondition($condition, $params = null)
	{
		$row = $this->getDb()->findByCondition($condition, $params);
		return $row;
	}

	/**
	 * 获取"SELECT SQL_CALC_FOUND_ROWS"语句的查询总数
	 * @return integer
	 */
	public function getFoundRows()
	{
		return $this->getDb()->getFoundRows();
	}

	/**
	 * 通过主键，字段名和字段值，编辑一条记录
	 * @param integer $pk
	 * @param string $columnName
	 * @param string $value
	 * @return integer
	 */
	public function singleModifyByPk($pk, $columnName, $value)
	{
		if (($pk = $this->cleanPositiveInteger($pk)) === false) {
			return false;
		}

		$rowCount = $this->getDb()->modifyByPk($pk, array($columnName => $value));
		return $rowCount;
	}

	/**
	 * 通过主键，编辑多条记录。不支持联合主键
	 * @param array $values
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk(array $values, array $params = array())
	{
		$formProcessor = $this->getFormProcessor();
		if (!$formProcessor->run(FormProcessor::OP_UPDATE, $params, $values)) {
			return false;
		}

		$attributes = $formProcessor->getValues();
		$rowCount = $this->getDb()->batchModifyByPk(implode(',', $formProcessor->id), $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，字段名和字段值，编辑多条记录
	 * @param array $pks
	 * @param string $columnName
	 * @param string $value
	 * @return integer
	 */
	public function batchSingleModifyByPk(array $pks, $columnName, $value)
	{
		if (($pks = $this->cleanPositiveInteger($pks)) === false) {
			return false;
		}

		$rowCount = $this->getDb()->batchModifyByPk(implode(',', $pks), array($columnName => $value));
		return $rowCount;
	}

	/**
	 * 通过主键，删除多条记录。不支持联合主键
	 * @param array $values
	 * @return integer
	 */
	public function batchRemoveByPk(array $values)
	{
		if (($values = $this->cleanPositiveInteger($values)) === false) {
			return false;
		}

		$rowCount = $this->getDb()->batchRemoveByPk(implode(',', $values));
		return $rowCount;
	}

	/**
	 * 通过主键，将一条记录移至回收站
	 * @param integer $pk
	 * @param string $columnName
	 * @param string $value
	 * @return integer
	 */
	public function trashByPk($pk, $columnName = 'trash', $value = 'y')
	{
		return $this->singleModifyByPk($pk, $columnName, $value);
	}

	/**
	 * 通过主键，将多条记录移至回收站。不支持联合主键
	 * @param array $pks
	 * @param string $columnName
	 * @param string $value
	 * @return integer
	 */
	public function batchTrashByPk(array $pks, $columnName = 'trash', $value = 'y')
	{
		return $this->batchSingleModifyByPk($pks, $columnName, $value);
	}

	/**
	 * 通过主键，从回收站还原一条记录
	 * @param integer $pk
	 * @param string $columnName
	 * @param string $value
	 * @return integer
	 */
	public function restoreByPk($pk, $columnName = 'trash', $value = 'n')
	{
		return $this->singleModifyByPk($pk, $columnName, $value);
	}

	/**
	 * 通过主键，将多条记录移至回收站。不支持联合主键
	 * @param array $pks
	 * @param string $columnName
	 * @param string $value
	 * @return integer
	 */
	public function batchRestoreByPk(array $pks, $columnName = 'trash', $value = 'n')
	{
		return $this->batchSingleModifyByPk($pks, $columnName, $value);
	}

	/**
	 * 设置数据库操作类
	 * @param \tdo\DynamicDb $db
	 * @return instance of libsrv\DynamicService
	 * @throws ErrorException 如果DB类不存在，抛出异常
	 * @throws ErrorException 如果获取的实例不是tdo\DynamicDb类的子类，抛出异常
	 */
	public function setDb(DynamicDb $db = null)
	{
		if ($db === null) {
			$className = $this->getSrvName() . '\\db\\' . $this->getClassName();
			if (!class_exists($className)) {
				throw new ErrorException(sprintf(
					'DynamicService is unable to find the DB class "%s".', $className
				));
			}

			$db = new $className($this->getTableName());
			if (!$db instanceof DynamicDb) {
				throw new ErrorException(sprintf(
					'DynamicService DB class "%s" is not instanceof tdo\DynamicDb.', $className
				));
			}
		}

		$this->_db = $db;
		return $this;
	}

	/**
	 * 获取表名
	 * @return string
	 */
	public function getTableName()
	{
		static $tableName = null;

		if ($tableName === null) {
			if (($tableName = trim($this->_tableName)) === '') {
				$tableName = $this->getClassName();
			}

			if ($this->_tableNum >= 0) {
				$tableName .= $this->_tableNumJoin . $this->_tableNum;
			}
		}

		return $tableName;
	}
}
