<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace builders\services;

use libsrv\DynamicService;

/**
 * Types class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-05-26 19:27:28Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class Types extends DynamicService
{
	/**
	 * @var string 表名
	 */
	protected $_tableName = 'builder_types';

	/**
	 * 获取所有的TypeName
	 * @return array
	 */
	public function getTypeNames()
	{
		$rows = (array) $this->findPairsByAttributes(array('type_id', 'type_name'), array(), 'sort');
		return $rows;
	}

	/**
	 * 通过“类型ID”获取“类型名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		$typeName = $this->getByPk('type_name', $typeId);
		return $typeName ? $typeName : '';
	}
}
