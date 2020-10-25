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
use tfc\ap\Ap;
use tfc\saf\Text;
use libapp\Model;
use menus\services\DataMenus;

/**
 * Menus class file
 * 菜单管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Menus.php 1 2014-10-22 16:43:30Z Code Generator $
 * @package modules.menus.model
 * @since 1.0
 */
class Menus extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'advanced' => array(
				'tid' => 'advanced',
				'prompt' => Text::_('MOD_MENUS_MENUS_VIEWTAB_ADVANCED_PROMPT')
			),
			'system' => array(
				'tid' => 'system',
				'prompt' => Text::_('MOD_MENUS_MENUS_VIEWTAB_SYSTEM_PROMPT')
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
			'menu_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MENUS_MENUS_MENU_ID_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_MENU_ID_HINT'),
			),
			'menu_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_MENU_NAME_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_MENU_NAME_HINT'),
				'required' => true,
			),
			'menu_pid' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MENUS_MENUS_MENU_PID_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_MENU_PID_HINT'),
			),
			'menu_url' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MENUS_MENUS_MENU_URL_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_MENU_URL_HINT'),
				'required' => true,
				'rows' => 3
			),
			'type_name' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => Text::_('MOD_MENUS_MENU_TYPES_TYPE_NAME_LABEL'),
			),
			'type_key' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_TYPE_KEY_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_TYPE_KEY_HINT'),
				'readonly' => true,
			),
			'picture' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_PICTURE_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_PICTURE_HINT'),
			),
			'alias' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_ALIAS_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ALIAS_HINT'),
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MENUS_MENUS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_DESCRIPTION_HINT'),
			),
			'allow_unregistered' => array(
				'__tid__' => 'advanced',
				'type' => 'switch',
				'label' => Text::_('MOD_MENUS_MENUS_ALLOW_UNREGISTERED_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ALLOW_UNREGISTERED_HINT'),
				'options' => DataMenus::getAllowUnregisteredEnum(),
				'value' => DataMenus::ALLOW_UNREGISTERED_Y,
			),
			'is_hide' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MENUS_MENUS_IS_HIDE_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_IS_HIDE_HINT'),
				'options' => DataMenus::getIsHideEnum(),
				'value' => DataMenus::IS_HIDE_N,
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_SORT_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_SORT_HINT'),
				'required' => true,
				'value' => 1000
			),
			'attr_target' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_ATTR_TARGET_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ATTR_TARGET_HINT'),
			),
			'attr_title' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_ATTR_TITLE_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ATTR_TITLE_HINT'),
			),
			'attr_rel' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_ATTR_REL_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ATTR_REL_HINT'),
			),
			'attr_class' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_ATTR_CLASS_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ATTR_CLASS_HINT'),
			),
			'attr_style' => array(
				'__tid__' => 'advanced',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_ATTR_STYLE_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_ATTR_STYLE_HINT'),
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_DT_CREATED_HINT'),
				'disabled' => true,
			),
			'dt_last_modified' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MENUS_MENUS_DT_LAST_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_MENUS_MENUS_DT_LAST_MODIFIED_HINT'),
				'disabled' => true,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“菜单名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getMenuNameLink($data)
	{
		$params = array(
			'id' => $data['menu_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['menu_name'], $url);
		return $output;
	}

	/**
	 * 获取type_key值
	 * @return string
	 */
	public function getTypeKey()
	{
		$typeKey = Ap::getRequest()->getTrim('type_key');
		if ($typeKey === '') {
			$id = Ap::getRequest()->getInteger('id');
			$typeKey = $this->getService()->getTypeKeyByMenuId($id);
		}

		return $typeKey;
	}

	/**
	 * 递归方式获取指定类型下的菜单，默认用|—填充子菜单左边用于和父菜单错位（可用于Table列表）
	 * @param string $typeKey
	 * @param integer $menuPid
	 * @param boolean $allowUnregistered
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function findLists($typeKey, $menuPid = 0, $allowUnregistered = true, $padStr = '|—', $leftPad = '', $rightPad = null)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findLists', array($typeKey, $menuPid, $allowUnregistered, $padStr, $leftPad, $rightPad));
		return $ret;
	}

	/**
	 * 递归方式获取指定类型下的菜单，默认用空格填充子菜单左边用于和父菜单错位
	 * （只返回ID和菜单名的键值对）（可用于Select表单的Option选项）
	 * @param string $typeKey
	 * @param integer $menuPid
	 * @param string $allowUnregistered
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function getOptions($typeKey, $menuPid = -1, $allowUnregistered = true, $padStr = '&nbsp;&nbsp;&nbsp;&nbsp;', $leftPad = '', $rightPad = null)
	{
		return $this->getService()->getOptions($typeKey, $menuPid, $allowUnregistered, $padStr, $leftPad, $rightPad);
	}

	/**
	 * 通过“类型Key”获取“类型名”
	 * @param string $typeKey
	 * @return string
	 */
	public function getTypeNameByTypeKey($typeKey)
	{
		return Model::getInstance('Types')->getTypeNameByTypeKey($typeKey);
	}

	/**
	 * 通过“主键ID”，获取“菜单名”
	 * @param integer $menuId
	 * @return string
	 */
	public function getMenuNameByMenuId($menuId)
	{
		return $this->getService()->getMenuNameByMenuId($menuId);
	}
}
