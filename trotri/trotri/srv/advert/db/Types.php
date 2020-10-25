<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\db;

use tdo\AbstractDb;
use advert\library\Constant;
use advert\library\TableNames;

/**
 * Types class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-10-23 22:38:25Z Code Generator $
 * @package advert.db
 * @since 1.0
 */
class Types extends AbstractDb
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
		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = 'SELECT ' . $option . ' `type_id`, `type_name`, `type_key`, `picture`, `description` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['type_name'])) {
			$typeName = trim($params['type_name']);
			if ($typeName !== '') {
				$condition .= ' AND `type_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['type_name'] = '%' . $typeName . '%';
			}
		}

		if (isset($params['type_key'])) {
			$typeKey = trim($params['type_key']);
			if ($typeKey !== '') {
				$condition .= ' AND `type_key` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['type_key'] = $typeKey;
			}
		}

		if (isset($params['picture'])) {
			$picture = trim($params['picture']);
			if ($picture !== '') {
				$condition .= ' AND `picture` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['picture'] = $picture;
			}
		}

		if (isset($params['type_id'])) {
			$typeId = (int) $params['type_id'];
			if ($typeId > 0) {
				$condition .= ' AND `type_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['type_id'] = $typeId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['type_name'])) {
				$attributes['type_name'] = $typeName;
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
	 * 通过主键，查询一条记录
	 * @param integer $typeId
	 * @return array
	 */
	public function findByPk($typeId)
	{
		if (($typeId = (int) $typeId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = 'SELECT `type_id`, `type_name`, `type_key`, `picture`, `description` FROM `' . $tableName . '` WHERE `type_id` = ?';
		return $this->fetchAssoc($sql, $typeId);
	}

	/**
	 * 通过位置Key，查询一条记录
	 * @param string $typeKey
	 * @return array
	 */
	public function findByTypeKey($typeKey)
	{
		if (($typeKey = trim($typeKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = 'SELECT `type_id`, `type_name`, `type_key`, `picture`, `description` FROM `' . $tableName . '` WHERE `type_key` = ?';
		return $this->fetchAssoc($sql, $typeKey);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$typeName = isset($params['type_name']) ? trim($params['type_name']) : '';
		$typeKey = isset($params['type_key']) ? trim($params['type_key']) : '';
		$picture = isset($params['picture']) ? trim($params['picture']) : '';
		$description = isset($params['description']) ? $params['description'] : '';

		if ($typeName === '' || $typeKey === '' || $picture === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$attributes = array(
			'type_name' => $typeName,
			'type_key' => $typeKey,
			'picture' => $picture,
			'description' => $description,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $typeId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($typeId, array $params = array())
	{
		if (($typeId = (int) $typeId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['type_name'])) {
			$typeName = trim($params['type_name']);
			if ($typeName !== '') {
				$attributes['type_name'] = $typeName;
			}
			else {
				return false;
			}
		}

		if (isset($params['type_key'])) {
			$typeKey = trim($params['type_key']);
			if ($typeKey !== '') {
				$attributes['type_key'] = $typeKey;
			}
			else {
				return false;
			}
		}

		if (isset($params['picture'])) {
			$picture = trim($params['picture']);
			if ($picture !== '') {
				$attributes['picture'] = $picture;
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

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`type_id` = ?');
		$attributes['type_id'] = $typeId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $typeId
	 * @return integer
	 */
	public function removeByPk($typeId)
	{
		if (($typeId = (int) $typeId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`type_id` = ?');
		$rowCount = $this->delete($sql, $typeId);
		return $rowCount;
	}
}
