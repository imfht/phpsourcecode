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

use libsrv\FormProcessor;
use tfc\validator;
use builders\library\Lang;

/**
 * FpValidators class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpValidators.php 1 2014-05-28 11:06:31Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class FpValidators extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'validator_name', 'field_id', 'options', 'option_category', 'message', 'sort', 'when')) {
				return false;
			}
		}

		$this->isValids($params, 'validator_name', 'field_id', 'options', 'option_category', 'message', 'sort', 'when');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		$rules = array(
			'validator_name' => 'trim',
			'field_id' => 'intval',
			'options' => 'trim',
			'option_category' => 'trim',
			'message' => 'trim',
			// 'sort' => 'intval',
			'when' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“验证类名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getValidatorNameRule($value)
	{
		$enum = DataValidators::getValidatorNameEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), Lang::_('SRV_FILTER_BUILDER_FIELD_VALIDATORS_VALIDATOR_NAME_INARRAY')),
		);
	}

	/**
	 * 获取“表单字段ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFieldIdRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELD_VALIDATORS_FIELD_ID_INTEGER')),
		);
	}

	/**
	 * 获取“验证时对比值类型”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getOptionCategoryRule($value)
	{
		$enum = DataValidators::getOptionCategoryEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_INARRAY'), implode(', ', $enum))),
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
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELD_VALIDATORS_SORT_NUMERIC')),
		);
	}

	/**
	 * 获取“验证环境”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getWhenRule($value)
	{
		$enum = DataValidators::getWhenEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELD_VALIDATORS_WHEN_INARRAY'), implode(', ', $enum))),
		);
	}

}
