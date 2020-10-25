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

use libsrv\FormProcessor;
use tfc\saf\Log;
use tfc\validator;
use libapp\ErrorNo;
use member\library\Lang;
use member\library\TableNames;

/**
 * FpTypes class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpTypes.php 1 2014-11-25 20:26:20Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class FpTypes extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'type_name', 'sort')) {
				return false;
			}
		}

		$this->isValids($params, 'type_name', 'sort', 'description');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if ($this->isUpdate()) {
			if (isset($params['type_name'])) {
				$row = $this->_object->findByPk($this->id);
				if (!$row || !is_array($row) || !isset($row['type_name'])) {
					Log::warning(sprintf(
						'FpTypes is unable to find the result by id "%d"', $this->id
					), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

					return false;
				}

				$typeName = trim($params['type_name']);
				if ($typeName === $row['type_name']) {
					unset($params['type_name']);
				}
			}
		}

		$rules = array(
			'type_name' => 'trim',
			'sort' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“类型名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTypeNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_MEMBER_TYPES_TYPE_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_MEMBER_TYPES_TYPE_NAME_MAXLENGTH')),
			'DbExists' => new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_MEMBER_TYPES_TYPE_NAME_UNIQUE'), $this->getDbProxy(), TableNames::getTypes(), 'type_name')
		);
	}

	/**
	 * 获取“排序”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getSortRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_MEMBER_TYPES_SORT_INTEGER')),
		);
	}

}
