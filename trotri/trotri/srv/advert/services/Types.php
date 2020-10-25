<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\services;

use libsrv\AbstractService;
use libsrv\Service;
use advert\library\Constant;

/**
 * Types class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-10-23 22:47:36Z Code Generator $
 * @package advert.services
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
	 * 通过位置Key，查询一条记录
	 * @param integer $typeKey
	 * @return array
	 */
	public function findByTypeKey($typeKey)
	{
		$row = $this->getDb()->findByTypeKey($typeKey);
		return $row;
	}

	/**
	 * 通过位置Key，获取某个列的值
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
	 * 通过位置Key，查询广告数
	 * @param string $typeKey
	 * @return integer
	 */
	public function getAdvertCount($typeKey)
	{
		return Service::getInstance('Adverts', 'advert')->countByTypeKey($typeKey);
	}

	/**
	 * 通过“示例图片”，获取“示例图片名”
	 * @param string $picture
	 * @return string
	 */
	public function getPictureLangByPicture($picture)
	{
		$enum = DataTypes::getPictureEnum();
		return isset($enum[$picture]) ? $enum[$picture] : '';
	}

	/**
	 * 通过“位置Key”，获取“位置名”
	 * @param string $typeKey
	 * @return string
	 */
	public function getTypeNameByTypeKey($typeKey)
	{
		$value = $this->getByTypeKey('type_name', $typeKey);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“位置名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		$value = $this->getByPk('type_name', $typeId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“位置Key”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeKeyByTypeId($typeId)
	{
		$value = $this->getByPk('type_key', $typeId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“示例图片”
	 * @param integer $typeId
	 * @return string
	 */
	public function getPictureByTypeId($typeId)
	{
		$value = $this->getByPk('picture', $typeId);
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
