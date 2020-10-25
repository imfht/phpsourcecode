<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\menus\model;

use library\BaseModel;
use tfc\saf\Text;

/**
 * Types class file
 * 菜单类型
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2014-10-22 10:47:54Z Code Generator $
 * @package modules.menus.model
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
				'label' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_ID_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_ID_HINT'),
			),
			'type_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_NAME_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_NAME_HINT'),
				'required' => true,
			),
			'type_key' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_KEY_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_KEY_HINT'),
				'required' => true,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MENUS_MENU_TYPES_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENU_TYPES_DESCRIPTION_HINT'),
			),
			'menu_count' => array(
				'label' => Text::_('MOD_MENUS_MENU_TYPES_MENU_COUNT_LABEL'),
			),
			'menus' => array(
				'label' => Text::_('MOD_MENUS_URLS_MENUS_INDEX'),
			)
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
	 * 通过“类型Key”，获取“类型名”
	 * @param string $typeKey
	 * @return string
	 */
	public function getTypeNameByTypeKey($typeKey)
	{
		return $this->getService()->getTypeNameByTypeKey($typeKey);
	}

	/**
	 * 通过类型Key，查询菜单数
	 * @param string $typeKey
	 * @return integer
	 */
	public function getMenuCount($typeKey)
	{
		$count = $this->getService()->getMenuCount($typeKey);
		return $count;
	}
}
