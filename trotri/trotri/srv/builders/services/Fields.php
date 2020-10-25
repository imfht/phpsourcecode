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
 * Fields class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Fields.php 1 2014-05-27 18:21:05Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class Fields extends DynamicService
{
	/**
	 * @var string 表名
	 */
	protected $_tableName = 'builder_fields';

	/**
	 * 通过“字段ID”获取“字段名”
	 * @param integer $fieldId
	 * @return string
	 */
	public function getFieldNameByFieldId($fieldId)
	{
		$fieldName = $this->getByPk('field_name', $fieldId);
		return $fieldName ? $fieldName : '';
	}

	/**
	 * 通过“字段ID”获取“生成代码ID”
	 * @param integer $fieldId
	 * @return integer
	 */
	public function getBuilderIdByFieldId($fieldId)
	{
		$builderId = (int) $this->getByPk('builder_id', $fieldId);
		return $builderId > 0 ? $builderId : 0;
	}

	/**
	 * 通过“字段ID”获取“Table和Form显示名”
	 * @param integer $fieldId
	 * @return string
	 */
	public function getHtmlLabelByFieldId($fieldId)
	{
		$htmlLabel = $this->getByPk('html_label', $fieldId);
		return $htmlLabel ? $htmlLabel : '';
	}
}
