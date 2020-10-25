<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\model;

use library\BaseModel;
use tfc\saf\Text;

/**
 * Types class file
 * 会员类型
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-11-25 20:26:20Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Types extends BaseModel
{
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
				'label' => Text::_('MOD_MEMBER_MEMBER_TYPES_TYPE_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_TYPES_TYPE_ID_HINT'),
			),
			'type_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_TYPES_TYPE_NAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_TYPES_TYPE_NAME_HINT'),
				'required' => true,
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_TYPES_SORT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_TYPES_SORT_HINT'),
				'required' => true,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MEMBER_MEMBER_TYPES_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_TYPES_DESCRIPTION_HINT'),
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
	 * 获取所有的类型
	 * @return array
	 */
	public function findAll()
	{
		$ret = $this->callFetchMethod($this->getService(), 'findAll');
		return $ret;
	}

	/**
	 * 获取所有的类型名称
	 * @return array
	 */
	public function getTypeNames()
	{
		return $this->getService()->getTypeNames();
	}

	/**
	 * 通过“主键ID”，获取“类型名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		return $this->getService()->getTypeNameByTypeId($typeId);
	}
}
