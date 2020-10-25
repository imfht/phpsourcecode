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
use libsrv\Service;

/**
 * FpFields class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpFields.php 1 2014-05-27 18:21:05Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class FpFields extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params,
				'field_name', 'column_length', 'column_auto_increment', 'column_unsigned',
				'column_comment', 'builder_id', 'group_id', 'type_id', 'sort',
				'html_label', 'form_prompt', 'form_required', 'form_modifiable', 'index_show', 'index_sort',
				'form_create_show', 'form_create_sort', 'form_modify_show', 'form_modify_sort', 'form_search_show', 'form_search_sort'
			)) {
				return false;
			}
		}

		$this->isValids($params,
			'field_name', 'column_length', 'column_auto_increment', 'column_unsigned', 'column_comment',
			'builder_id', 'group_id', 'type_id', 'sort', 'html_label',
			'form_prompt', 'form_required', 'form_modifiable', 'index_show', 'index_sort',
			'form_create_show', 'form_create_sort', 'form_modify_show', 'form_modify_sort', 'form_search_show', 'form_search_sort'
		);

		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		$rules = array(
			'field_name' => 'trim',
			'column_length' => 'trim',
			'column_auto_increment' => 'trim',
			'column_unsigned' => 'trim',
			'column_comment' => 'trim',
			'builder_id' => 'intval',
			'group_id' => 'intval',
			'type_id' => 'intval',
			// 'sort' => 'intval',
			'html_label' => 'trim',
			'form_prompt' => 'trim',
			'form_required' => 'trim',
			'form_modifiable' => 'trim',
			'index_show' => 'trim',
			'index_sort' => 'intval',
			'form_create_show' => 'trim',
			'form_create_sort' => 'intval',
			'form_modify_show' => 'trim',
			'form_modify_sort' => 'intval',
			'form_search_show' => 'trim',
			'form_search_sort' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“字段名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFieldNameRule($value)
	{
		return array(
			'AlphaNum' => new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_FIELD_NAME_ALPHANUM')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_BUILDER_FIELDS_FIELD_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_BUILDER_FIELDS_FIELD_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“是否自动递增”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getColumnAutoIncrementRule($value)
	{
		$enum = DataFields::getColumnAutoIncrementEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_COLUMN_AUTO_INCREMENT_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否无符号”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getColumnUnsignedRule($value)
	{
		$enum = DataFields::getColumnUnsignedEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_COLUMN_UNSIGNED_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“DB字段描述”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getColumnCommentRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_COLUMN_COMMENT_NOTEMPTY')),
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
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_BUILDER_ID_INTEGER')),
		);
	}

	/**
	 * 获取“表单字段组ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getGroupIdRule($value)
	{
		$enum = Service::getInstance('Groups', 'builders')->getPromptsByBuilderId($this->builder_id, true);
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), Lang::_('SRV_FILTER_BUILDER_FIELDS_GROUP_ID_INARRAY')),
		);
	}

	/**
	 * 获取“字段类型ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTypeIdRule($value)
	{
		$enum = Service::getInstance('Types', 'builders')->getTypeNames();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), Lang::_('SRV_FILTER_BUILDER_FIELDS_TYPE_ID_INARRAY')),
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
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_SORT_NUMERIC')),
		);
	}

	/**
	 * 获取“Table和Form显示名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getHtmlLabelRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_HTML_LABEL_NOTEMPTY')),
		);
	}

	/**
	 * 获取“表单是否必填”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormRequiredRule($value)
	{
		$enum = DataFields::getFormRequiredEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_REQUIRED_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“编辑表单是否中允许输入”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormModifiableRule($value)
	{
		$enum = DataFields::getFormModifiableEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_MODIFIABLE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否在列表中展示”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIndexShowRule($value)
	{
		$enum = DataFields::getIndexShowEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_INDEX_SHOW_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“在列表中排序”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getIndexSortRule($value)
	{
		return array(
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_INDEX_SORT_NUMERIC')),
		);
	}

	/**
	 * 获取“是否在新增表单中展示”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormCreateShowRule($value)
	{
		$enum = DataFields::getFormCreateShowEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_CREATE_SHOW_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“在新增表单中排序”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormCreateSortRule($value)
	{
		return array(
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_CREATE_SORT_NUMERIC')),
		);
	}

	/**
	 * 获取“是否在编辑表单中展示”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormModifyShowRule($value)
	{
		$enum = DataFields::getFormModifyShowEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_MODIFY_SHOW_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“在编辑表单中排序”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormModifySortRule($value)
	{
		return array(
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_MODIFY_SORT_NUMERIC')),
		);
	}

	/**
	 * 获取“是否在查询表单中展示”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormSearchShowRule($value)
	{
		$enum = DataFields::getFormSearchShowEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_SEARCH_SHOW_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“在查询表单中排序”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getFormSearchSortRule($value)
	{
		return array(
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_BUILDER_FIELDS_FORM_SEARCH_SORT_NUMERIC')),
		);
	}

}
