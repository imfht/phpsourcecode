<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace system\db;

use tdo\AbstractDb;
use system\library\Constant;
use system\library\TableNames;

/**
 * Options class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Options.php 1 2014-08-17 21:37:12Z Code Generator $
 * @package system.db
 * @since 1.0
 */
class Options extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 获取所有的配置，以键值对方式返回
	 * @return array
	 */
	public function findPairs()
	{
		$tableName = $this->getTblprefix() . TableNames::getOptions();

		$sql = 'SELECT `option_key`, `option_value` FROM `' . $tableName . '`';
		return $this->fetchPairs($sql);
	}

	/**
	 * 获取所有的配置
	 * @return array
	 */
	public function findAll()
	{
		$tableName = $this->getTblprefix() . TableNames::getOptions();

		$sql = 'SELECT `option_id`, `option_key`, `option_value` FROM `' . $tableName . '`';
		return $this->fetchAll($sql);
	}

	/**
	 * 通过键名，编辑多条记录，如果键名不存在则新增记录
	 * @param array $params
	 * @return integer
	 */
	public function batchReplace(array $params = array())
	{
		$rowCount = 0;

		foreach ($params as $optKey => $optValue) {
			if (($result = $this->replace($optKey, $optValue)) !== false) {
				$rowCount++;
			}
		}

		return $rowCount;
	}

	/**
	 * 通过键名，编辑一条记录，如果键名不存在则新增一条记录
	 * @param string $optKey
	 * @param mixed $optValue
	 * @return integer
	 */
	public function replace($optKey, $optValue)
	{
		if (($optKey = trim($optKey)) === '') {
			return false;
		}

		$oldValue = $this->getValueByKey($optKey);
		if ($oldValue === false) {
			return $this->create($optKey, $optValue);
		}

		if ($oldValue !== $optValue) {
			return $this->modifyByKey($optKey, $optValue);
		}

		return true;
	}

	/**
	 * 通过主键，获取配置值
	 * @param integer $optId
	 * @return mixed
	 */
	public function getValueByPk($optId)
	{
		if (($optId = (int) $optId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = 'SELECT `option_value` FROM ' . $tableName . ' WHERE `option_id` = ?';
		return $this->fetchColumn($sql, $optId);
	}

	/**
	 * 通过键名，获取配置值
	 * @param string $optKey
	 * @return mixed
	 */
	public function getValueByKey($optKey)
	{
		if (($optKey = trim($optKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = 'SELECT `option_value` FROM ' . $tableName . ' WHERE `option_key` = ?';
		return $this->fetchColumn($sql, $optKey);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $optId
	 * @return array
	 */
	public function findByPk($optId)
	{
		if (($optId = (int) $optId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = 'SELECT `option_id`, `option_key`, `option_value` FROM `' . $tableName . '` WHERE `option_id` = ?';
		return $this->fetchAssoc($sql, $optId);
	}

	/**
	 * 通过键名，查询一条记录
	 * @param string $optKey
	 * @return array
	 */
	public function findByKey($optKey)
	{
		if (($optKey = trim($optKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = 'SELECT `option_id`, `option_key`, `option_value` FROM `' . $tableName . '` WHERE `option_key` = ?';
		return $this->fetchAssoc($sql, $optKey);
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $optId
	 * @param mixed $optValue
	 * @return integer
	 */
	public function modifyByPk($optId, $optValue)
	{
		if (($optId = (int) $optId) <= 0) {
			return false;
		}

		$attributes = array(
			'option_value' => $optValue
		);

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`option_id` = ?');
		$attributes['option_id'] = $optId;
		return $this->update($sql, $attributes);
	}

	/**
	 * 通过键名，编辑一条记录
	 * @param string $optKey
	 * @param mixed $optValue
	 * @return integer
	 */
	public function modifyByKey($optKey, $optValue)
	{
		if (($optKey = trim($optKey)) === '') {
			return false;
		}

		$attributes = array(
			'option_value' => $optValue
		);

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`option_key` = ?');
		$attributes['option_key'] = $optKey;
		return $this->update($sql, $attributes);
	}

	/**
	 * 新增一条记录
	 * @param string $optKey
	 * @param mixed $optValue
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create($optKey, $optValue, $ignore = false)
	{
		if (($optKey = trim($optKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$attributes = array(
			'option_key' => $optKey,
			'option_value' => $optValue
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $optId
	 * @return integer
	 */
	public function removeByPk($optId)
	{
		if (($optId = (int) $optId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`option_id` = ?');
		$rowCount = $this->delete($sql, $optId);
		return $rowCount;
	}

	/**
	 * 通过键名，删除一条记录
	 * @param string $optKey
	 * @return integer
	 */
	public function removeByKey($optKey)
	{
		if (($optKey = trim($optKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getOptions();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`option_key` = ?');
		$rowCount = $this->delete($sql, $optKey);
		return $rowCount;
	}
}
