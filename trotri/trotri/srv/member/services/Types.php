<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use libsrv\AbstractService;

/**
 * Types class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-11-25 20:26:20Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class Types extends AbstractService
{
	/**
	 * 获取所有的类型
	 * @return array
	 */
	public function findAll()
	{
		$rows = $this->getDb()->findAll();
		return $rows;
	}

	/**
	 * 获取所有的类型Id
	 * @return array
	 */
	public function getTypeIds()
	{
		$rows = $this->getDb()->getTypeIds();
		return $rows;
	}

	/**
	 * 获取所有的类型名称
	 * @return array
	 */
	public function getTypeNames()
	{
		$rows = $this->getDb()->getTypeNames();
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
	 * 通过“主键ID”，获取“排序”
	 * @param integer $typeId
	 * @return integer
	 */
	public function getSortByTypeId($typeId)
	{
		$value = $this->getByPk('sort', $typeId);
		return $value ? (int) $value : 0;
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
