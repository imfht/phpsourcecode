<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\db;

use tdo\AbstractDb;
use member\library\Constant;
use member\library\TableNames;

/**
 * Types class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-11-25 20:26:20Z Code Generator $
 * @package member.db
 * @since 1.0
 */
class Types extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 获取所有的类型
	 * @return array
	 */
	public function findAll()
	{
		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = 'SELECT `type_id`, `type_name`, `sort`, `description` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchAll($sql);
	}

	/**
	 * 获取所有的类型Id
	 * @return array
	 */
	public function getTypeIds()
	{
		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = 'SELECT `type_id` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchAll($sql);
	}

	/**
	 * 获取所有的类型名称
	 * @return array
	 */
	public function getTypeNames()
	{
		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = 'SELECT `type_id`, `type_name` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchPairs($sql);
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
		$sql = 'SELECT `type_id`, `type_name`, `sort`, `description` FROM `' . $tableName . '` WHERE `type_id` = ?';
		return $this->fetchAssoc($sql, $typeId);
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
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$description = isset($params['description']) ? trim($params['description']) : '';

		if ($typeName === '' || $sort <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$attributes = array(
			'type_name' => $typeName,
			'sort' => $sort,
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

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort > 0) {
				$attributes['sort'] = $sort;
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
		if (($typeId = (int) $typeId) <= 1) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getTypes();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`type_id` = ?');
		$rowCount = $this->delete($sql, $typeId);
		return $rowCount;
	}
}
