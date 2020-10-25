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
use builders\services\DataValidators;
use libapp\Model;
use library\Constant;

/**
 * Validators class file
 * 表单字段验证
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Validators.php 1 2014-05-28 11:06:31Z Code Generator $
 * @package modules.builder.model
 * @since 1.0
 */
class Validators extends BaseModel
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
			'validator_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_VALIDATOR_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_VALIDATOR_ID_HINT'),
			),
			'validator_name' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_VALIDATOR_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_VALIDATOR_NAME_HINT'),
				'options' => DataValidators::getValidatorNameEnum(),
				'value' => 'Integer',
			),
			'field_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_FIELD_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_FIELD_ID_HINT'),
			),
			'field_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELDS_FIELD_NAME_LABEL'),
				'readonly' => true,
			),
			'options' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_OPTIONS_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_OPTIONS_HINT'),
			),
			'option_category' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_HINT'),
				'options' => DataValidators::getOptionCategoryEnum(),
				'value' => DataValidators::OPTION_CATEGORY_INTEGER,
			),
			'message' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_MESSAGE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_MESSAGE_HINT'),
				'value' => '',
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_SORT_HINT'),
				'required' => true,
			),
			'when' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_WHEN_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_VALIDATORS_WHEN_HINT'),
				'options' => DataValidators::getWhenEnum(),
				'value' => DataValidators::WHEN_ALL,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“验证类名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getValidatorNameLink($data)
	{
		$params = array(
			'id' => $data['validator_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['validator_name'], $url);
		return $output;
	}

	/**
	 * 获取field_id值
	 * @return integer
	 */
	public function getFieldId()
	{
		$fieldId = Ap::getRequest()->getInteger('field_id');
		if ($fieldId <= 0) {
			$id = Ap::getRequest()->getInteger('id');
			$fieldId = $this->getService()->getByPk('field_id', $id);
		}

		return $fieldId;
	}

	/**
	 * 通过“字段ID”获取“字段名”
	 * @param integer $fieldId
	 * @return string
	 */
	public function getFieldNameByFieldId($fieldId)
	{
		return Model::getInstance('Fields')->getFieldNameByFieldId($fieldId);
	}

	/**
	 * 通过“字段ID”获取“Table和Form显示名”
	 * @param integer $fieldId
	 * @return string
	 */
	public function getHtmlLabelByFieldId($fieldId)
	{
		return Model::getInstance('Fields')->getHtmlLabelByFieldId($fieldId);
	}

	/**
	 * 获取验证时对比值类型
	 * @param string $optionCategory
	 * @return string
	 */
	public function getOptionCategoryLangByOptionCategory($optionCategory)
	{
		return $this->getService()->getOptionCategoryLangByOptionCategory($optionCategory);
	}

	/**
	 * 获取验证时对比值类型
	 * @param string $when
	 * @return string
	 */
	public function getWhenLangByWhen($when)
	{
		return $this->getService()->getWhenLangByWhen($when);
	}

	/**
	 * 获取“出错提示消息”所有选项
	 * @return array
	 */
	public function getMessageEnum()
	{
		$enum = DataValidators::getMessageEnum();
		return $enum;
	}

	/**
	 * 获取“验证时对比值类型”所有选项
	 * @return array
	 */
	public function getOptionCategoryEnum()
	{
		$enum = DataValidators::getOptionCategoryEnum();
		return $enum;
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
		$fieldId = isset($params['field_id']) ? (int) $params['field_id'] : 0;
		$params = array();
		if ($fieldId >= 0) {
			$params['field_id'] = $fieldId;
		}

		$ret = parent::search($params, 'sort', $limit, $offset);
		return $ret;
	}
}
