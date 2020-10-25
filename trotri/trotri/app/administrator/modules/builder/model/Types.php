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
use tfc\saf\Text;
use builders\services\DataTypes;
use library\Constant;

/**
 * Types class file
 * 表单字段类型
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-05-26 19:27:28Z Code Generator $
 * @package modules.builder.model
 * @since 1.0
 */
class Types extends BaseModel
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
			'type_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDER_TYPES_TYPE_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_TYPES_TYPE_ID_HINT'),
			),
			'type_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_TYPES_TYPE_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_TYPES_TYPE_NAME_HINT'),
				'required' => true,
			),
			'form_type' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_TYPES_FORM_TYPE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_TYPES_FORM_TYPE_HINT'),
				'required' => true,
			),
			'field_type' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_TYPES_FIELD_TYPE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_TYPES_FIELD_TYPE_HINT'),
				'required' => true,
			),
			'category' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_BUILDER_BUILDER_TYPES_CATEGORY_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_TYPES_CATEGORY_HINT'),
				'options' => DataTypes::getCategoryEnum(),
				'value' => DataTypes::CATEGORY_TEXT,
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_TYPES_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_TYPES_SORT_HINT'),
				'required' => true,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“类型名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getTypeNameLink($data)
	{
		$params = array(
			'id' => $data['type_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['type_name'], $url);
		return $output;
	}

	/**
	 * 通过“类型ID”获取“类型名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		return $this->getService()->getTypeNameByTypeId($typeId);
	}

	/**
	 * 获取所有的TypeName
	 * @return array
	 */
	public function getTypeNames()
	{
		return $this->getService()->getTypeNames();
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
		$ret = parent::search(array(), '', $limit, $offset);
		return $ret;
	}
}
