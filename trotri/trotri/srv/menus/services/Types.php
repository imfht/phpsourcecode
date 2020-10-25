<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace menus\services;

use libsrv\AbstractService;
use libsrv\Service;
use menus\library\Constant;

/**
 * Types class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-10-22 10:08:47Z Code Generator $
 * @package menus.services
 * @since 1.0
 */
class Types extends AbstractService
{
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
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $typeId
	 * @return array
	 */
	public function findByPk($typeId)
	{
		$row = $this->getDb()->findByPk($typeId);
		return $row;
	}

	/**
	 * 通过类型Key，查询一条记录
	 * @param string $typeKey
	 * @return array
	 */
	public function findByTypeKey($typeKey)
	{
		$row = $this->getDb()->findByTypeKey($typeKey);
		return $row;
	}

	/**
	 * 通过类型Key，获取某个列的值
	 * @param string $columnName
	 * @param integer $value
	 * @return mixed
	 */
	public function getByTypeKey($columnName, $value)
	{
		$row = $this->findByTypeKey($value);
		if ($row && is_array($row) && isset($row[$columnName])) {
			return $row[$columnName];
		}

		return false;
	}

	/**
	 * 通过类型Key，查询菜单数
	 * @param string $typeKey
	 * @return integer
	 */
	public function getMenuCount($typeKey)
	{
		return Service::getInstance('Menus', 'menus')->countByTypeKey($typeKey);
	}

	/**
	 * 通过“类型Key”，获取“类型名”
	 * @param string $typeKey
	 * @return string
	 */
	public function getTypeNameByTypeKey($typeKey)
	{
		$value = $this->getByTypeKey('type_name', $typeKey);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“类型Key”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeKeyByTypeId($typeId)
	{
		$value = $this->getByPk('type_key', $typeId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“类型名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		$value = $this->getByPk('type_name', $typeId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $typeId
	 * @return string
	 */
	public function getDescriptionByTypeId($typeId)
	{
		$value = $this->getByPk('description', $typeId);
		return $value ? $value : '';
	}

}
