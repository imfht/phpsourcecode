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
 * FpGroups class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpGroups.php 1 2014-05-27 17:10:14Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class FpGroups extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'group_name', 'prompt', 'builder_id', 'sort', 'description')) {
				return false;
			}
		}

		$this->isValids($params, 'group_name', 'prompt', 'builder_id', 'sort', 'description');
		return !$this->hasError();
	}

	/**
	 * 获取“组名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getGroupNameRule($value)
	{
		return array(
			'Alpha' => new validator\AlphaValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_GROUP_NAME_ALPHA')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_GROUP_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 12, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_GROUP_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“提示”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPromptRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_PROMPT_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 12, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_PROMPT_MAXLENGTH')),
		);
	}

	/**
	 * 获取“生成代码ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getBuilderIdRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_BUILDER_ID_INTEGER')),
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
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELD_GROUPS_SORT_NUMERIC')),
		);
	}

}
