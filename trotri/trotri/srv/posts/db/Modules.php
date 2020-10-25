<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\db;

use tdo\AbstractDb;
use posts\library\Constant;
use posts\library\TableNames;

/**
 * Modules class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modules.php 1 2014-10-12 21:47:03Z Code Generator $
 * @package posts.db
 * @since 1.0
 */
class Modules extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getModules();
		$sql = 'SELECT ' . $option . ' `module_id`, `module_name`, `fields`, `forbidden`, `description` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['module_name'])) {
			$moduleName = trim($params['module_name']);
			if ($moduleName !== '') {
				$condition .= ' AND `module_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['module_name'] = '%' . $moduleName . '%';
			}
		}

		if (isset($params['forbidden'])) {
			$forbidden = trim($params['forbidden']);
			if ($forbidden !== '') {
				$condition .= ' AND `forbidden` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['forbidden'] = $forbidden;
			}
		}

		if (isset($params['module_id'])) {
			$moduleId = (int) $params['module_id'];
			if ($moduleId > 0) {
				$condition .= ' AND `module_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['module_id'] = $moduleId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['module_name'])) {
				$attributes['module_name'] = $moduleName;
			}
			if (is_array($ret)) {
				$ret['attributes'] = $attributes;
				$ret['order']      = $order;
				$ret['limit']      = $limit;
				$ret['offset']     = $offset;
			}
		}
		else {
			$ret = $this->fetchAll($sql, $attributes);
		}

		return $ret;
	}

	/**
	 * 获取所有的模型名称
	 * @return array
	 */
	public function getModuleNames()
	{
		$tableName = $this->getTblprefix() . TableNames::getModules();
		$sql = 'SELECT `module_id`, `module_name` FROM `' . $tableName . '` WHERE `forbidden` = ?';
		return $this->fetchPairs($sql, 'n');
	}

	/**
	 * 获取所有的文档扩展字段
	 * @return array
	 */
	public function getFields()
	{
		$tableName = $this->getTblprefix() . TableNames::getModules();
		$sql = 'SELECT `module_id`, `fields` FROM `' . $tableName . '` WHERE `forbidden` = ?';
		return $this->fetchPairs($sql, 'n');
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $moduleId
	 * @return array
	 */
	public function findByPk($moduleId)
	{
		if (($moduleId = (int) $moduleId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getModules();
		$sql = 'SELECT `module_id`, `module_name`, `fields`, `forbidden`, `description` FROM `' . $tableName . '` WHERE `module_id` = ?';
		return $this->fetchAssoc($sql, $moduleId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$moduleName = isset($params['module_name']) ? trim($params['module_name']) : '';
		$fields = isset($params['fields']) ? trim($params['fields']) : '';
		$forbidden = isset($params['forbidden']) ? trim($params['forbidden']) : '';
		$description = isset($params['description']) ? trim($params['description']) : '';

		if ($moduleName === '') {
			return false;
		}

		if ($forbidden === '') {
			$forbidden = 'n';
		}

		$tableName = $this->getTblprefix() . TableNames::getModules();
		$attributes = array(
			'module_name' => $moduleName,
			'fields' => $fields,
			'forbidden' => $forbidden,
			'description' => $description,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $moduleId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($moduleId, array $params = array())
	{
		if (($moduleId = (int) $moduleId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['module_name'])) {
			$moduleName = trim($params['module_name']);
			if ($moduleName !== '') {
				$attributes['module_name'] = $moduleName;
			}
			else {
				return false;
			}
		}

		if (isset($params['fields'])) {
			$attributes['fields'] = $params['fields'];
		}

		if (isset($params['forbidden'])) {
			$forbidden = trim($params['forbidden']);
			if ($forbidden !== '') {
				$attributes['forbidden'] = $forbidden;
			}
			else {
				return false;
			}
		}

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getModules();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`module_id` = ?');
		$attributes['module_id'] = $moduleId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $moduleId
	 * @return integer
	 */
	public function removeByPk($moduleId)
	{
		if (($moduleId = (int) $moduleId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getModules();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`module_id` = ?');
		$rowCount = $this->delete($sql, $moduleId);
		return $rowCount;
	}
}
