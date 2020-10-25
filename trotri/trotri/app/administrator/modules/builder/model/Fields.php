<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\model;

use library\BaseModel;
use tfc\ap\Ap;
use tfc\saf\Text;
use libapp\Model;
use builders\services\DataFields;
use library\Constant;

/**
 * Fields class file
 * 表单字段
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Fields.php 1 2014-05-27 18:21:05Z Code Generator $
 * @package modules.builder.model
 * @since 1.0
 */
class Fields extends BaseModel
{
	/**
	 * @var string 业务层名
	 */
	protected $_srvName = Constant::SRV_NAME_BUILDERS;

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'view' => array(
				'tid' => 'view',
				'prompt' => Text::_('MOD_BUILDER_BUILDER_FIELDS_VIEWTAB_VIEW_PROMPT')
			),
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$output = array(
			'field_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FIELD_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FIELD_ID_HINT'),
			),
			'field_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FIELD_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FIELD_NAME_HINT'),
				'required' => true,
			),
			'column_length' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_LENGTH_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_LENGTH_HINT'),
			),
			'column_auto_increment' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_AUTO_INCREMENT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_AUTO_INCREMENT_HINT'),
				'options' => DataFields::getColumnAutoIncrementEnum(),
				'value' => DataFields::COLUMN_AUTO_INCREMENT_N,
			),
			'column_unsigned' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_UNSIGNED_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_UNSIGNED_HINT'),
				'options' => DataFields::getColumnUnsignedEnum(),
				'value' => DataFields::COLUMN_UNSIGNED_N,
			),
			'column_comment' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_COMMENT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_COLUMN_COMMENT_HINT'),
				'required' => true,
			),
			'builder_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_BUILDER_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_BUILDER_ID_HINT'),
			),
			'builder_name' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => Text::_('MOD_BUILDER_BUILDERS_BUILDER_NAME_LABEL'),
			),
			'group_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_GROUP_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_GROUP_ID_HINT'),
			),
			'type_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_TYPE_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_TYPE_ID_HINT'),
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_SORT_HINT'),
				'required' => true,
			),
			'html_label' => array(
				'__tid__' => 'view',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_HTML_LABEL_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_HTML_LABEL_HINT'),
				'required' => true,
			),
			'form_prompt' => array(
				'__tid__' => 'view',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_PROMPT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_PROMPT_HINT'),
			),
			'form_prompt_examples' => array(
				'__tid__' => 'view',
				'type' => 'select',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_PROMPT_EXAMPLES_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_PROMPT_EXAMPLES_HINT'),
				'options' => DataFields::getFormPromptExamplesEnum(),
			),
			'form_required' => array(
				'__tid__' => 'view',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_REQUIRED_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_REQUIRED_HINT'),
				'options' => DataFields::getFormRequiredEnum(),
				'value' => DataFields::FORM_REQUIRED_Y,
			),
			'form_modifiable' => array(
				'__tid__' => 'view',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_MODIFIABLE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_MODIFIABLE_HINT'),
				'options' => DataFields::getFormModifiableEnum(),
				'value' => DataFields::FORM_MODIFIABLE_N,
			),
			'index_show' => array(
				'__tid__' => 'view',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_INDEX_SHOW_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_INDEX_SHOW_HINT'),
				'options' => DataFields::getIndexShowEnum(),
				'value' => DataFields::INDEX_SHOW_N,
			),
			'index_sort' => array(
				'__tid__' => 'view',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_INDEX_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_INDEX_SORT_HINT'),
				'value' => 0,
				'required' => true,
			),
			'form_create_show' => array(
				'__tid__' => 'view',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_CREATE_SHOW_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_CREATE_SHOW_HINT'),
				'options' => DataFields::getFormCreateShowEnum(),
				'value' => DataFields::FORM_CREATE_SHOW_N,
			),
			'form_create_sort' => array(
				'__tid__' => 'view',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_CREATE_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_CREATE_SORT_HINT'),
				'value' => 0,
				'required' => true,
			),
			'form_modify_show' => array(
				'__tid__' => 'view',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_MODIFY_SHOW_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_MODIFY_SHOW_HINT'),
				'options' => DataFields::getFormModifyShowEnum(),
				'value' => DataFields::FORM_MODIFY_SHOW_N,
			),
			'form_modify_sort' => array(
				'__tid__' => 'view',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_MODIFY_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_MODIFY_SORT_HINT'),
				'value' => 0,
				'required' => true,
			),
			'form_search_show' => array(
				'__tid__' => 'view',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_SEARCH_SHOW_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_SEARCH_SHOW_HINT'),
				'options' => DataFields::getFormSearchShowEnum(),
				'value' => DataFields::FORM_SEARCH_SHOW_N,
			),
			'form_search_sort' => array(
				'__tid__' => 'view',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_SEARCH_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FORM_SEARCH_SORT_HINT'),
				'value' => 0,
				'required' => true,
			),
			'builder_field_validators' => array(
				'label' => Text::_('MOD_BUILDER_URLS_VALIDATORS_INDEX'),
			)
		);

		return $output;
	}

	/**
	 * 获取列表页“字段名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getFieldNameLink($data)
	{
		$params = array(
			'id' => $data['field_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['field_name'], $url);
		return $output;
	}

	/**
	 * 获取builder_id值
	 * @return integer
	 */
	public function getBuilderId()
	{
		$builderId = Ap::getRequest()->getInteger('builder_id');
		if ($builderId <= 0) {
			$id = Ap::getRequest()->getInteger('id');
			$builderId = $this->getService()->getByPk('builder_id', $id);
		}

		return $builderId;
	}

	/**
	 * 通过“生成代码ID”获取“生成代码名”
	 * @param integer $builderId
	 * @return string
	 */
	public function getBuilderNameByBuilderId($builderId)
	{
		return Model::getInstance('Builders')->getBuilderNameByBuilderId($builderId);
	}

	/**
	 * 通过“字段组ID”获取“字段组名”
	 * @param integer $groupId
	 * @return string
	 */
	public function getGroupNameByGroupId($groupId)
	{
		return Model::getInstance('Groups')->getGroupNameByGroupId($groupId);
	}

	/**
	 * 通过“字段组ID”获取“字段组提示”
	 * @param integer $groupId
	 * @return string
	 */
	public function getPromptByGroupId($groupId)
	{
		return Model::getInstance('Groups')->getPromptByGroupId($groupId);
	}

	/**
	 * 通过“类型ID”获取“类型名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		return Model::getInstance('Types')->getTypeNameByTypeId($typeId);
	}

	/**
	 * 通过builder_id获取所有的Groups
	 * @param integer $builderId
	 * @param boolean $joinDafault
	 * @return array
	 */
	public function getGroupsByBuilderId($builderId, $joinDafault = false)
	{
		return Model::getInstance('Groups')->getPromptsByBuilderId($builderId, $joinDafault);
	}

	/**
	 * 获取所有的TypeName
	 * @return array
	 */
	public function getTypeNames()
	{
		return Model::getInstance('Types')->getTypeNames();
	}

	/**
	 * 通过“字段ID”获取“字段名”
	 * @param integer $fieldId
	 * @return string
	 */
	public function getFieldNameByFieldId($fieldId)
	{
		return $this->getService()->getFieldNameByFieldId($fieldId);
	}

	/**
	 * 通过“字段ID”获取“生成代码ID”
	 * @param integer $fieldId
	 * @return integer
	 */
	public function getBuilderIdByFieldId($fieldId)
	{
		return $this->getService()->getBuilderIdByFieldId($fieldId);
	}

	/**
	 * 通过“字段ID”获取“Table和Form显示名”
	 * @param integer $fieldId
	 * @return string
	 */
	public function getHtmlLabelByFieldId($fieldId)
	{
		return $this->getService()->getHtmlLabelByFieldId($fieldId);
	}

	/**
	 * 查询数据列表
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function search(array $params = array(), $order = '', $limit = null, $offset = null)
	{
		$builderId = isset($params['builder_id']) ? (int) $params['builder_id'] : 0;
		$params = array();
		if ($builderId >= 0) {
			$params['builder_id'] = $builderId;
		}

		$ret = parent::search($params, $order, $limit, $offset);
		return $ret;
	}
}
