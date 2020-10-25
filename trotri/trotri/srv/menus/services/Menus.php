<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace menus\services;

use libsrv\AbstractService;
use menus\library\Lang;

/**
 * Menus class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Menus.php 1 2014-10-22 14:27:46Z Code Generator $
 * @package menus.services
 * @since 1.0
 */
class Menus extends AbstractService
{
	/**
	 * 递归获取指定类型下的所有菜单
	 * @param string $typeKey
	 * @param integer $menuPid
	 * @param boolean $allowUnregistered
	 * @return array
	 */
	public function findRows($typeKey, $menuPid = 0, $allowUnregistered = true)
	{
		$rows = $this->findAllByKeyPid($typeKey, $menuPid, $allowUnregistered);
		if (!$rows || !is_array($rows)) {
			return array();
		}

		$data = array();
		foreach ($rows as $row) {
			if ($row['is_hide'] !== DataMenus::IS_HIDE_N) {
				continue;
			}

			$tmpRows = $this->findRows($typeKey, $row['menu_id'], $allowUnregistered);
			$row['data'] = $tmpRows;
			$data[] = $row;
		}

		return $data;
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
		$rows = $this->findAllByKeyPid($typeKey, $menuPid, $allowUnregistered);
		if (!$rows || !is_array($rows)) {
			return array();
		}

		$tmpLeftPad = is_string($leftPad) ? $leftPad . $padStr : null;
		$tmpRightPad = is_string($rightPad) ? $rightPad . $padStr : null;

		$data = array();
		foreach ($rows as $row) {
			$row['menu_name'] = $leftPad . $row['menu_name'] . $rightPad;
			$data[] = $row;

			$tmpRows = $this->findLists($typeKey, $row['menu_id'], $allowUnregistered, $padStr, $tmpLeftPad, $tmpRightPad);
			$data = array_merge($data, $tmpRows);
		}

		return $data;
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
		if ($menuPid === -1) {
			$tmpLeftPad = is_string($leftPad) ? $leftPad . $padStr : null;
			$tmpRightPad = is_string($rightPad) ? $rightPad . $padStr : null;

			$data = array(0 => Lang::_('SRV_ENUM_MENUS_MENU_TOP'));
			$data += $this->getOptions($typeKey, 0, $allowUnregistered, $padStr, $tmpLeftPad, $tmpRightPad);
			return $data;
		}

		$data = array();

		$rows = $this->findLists($typeKey, $menuPid, $allowUnregistered, $padStr, $leftPad, $rightPad);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				if (!isset($row['menu_id']) || !isset($row['menu_name'])) {
					continue;
				}

				$menuId = (int) $row['menu_id'];
				$data[$menuId] = $row['menu_name'];
			}
		}

		return $data;
	}

	/**
	 * 方式获取指定类型下的菜单
	 * @param string $typeKey
	 * @param integer $menuPid
	 * @param boolean $allowUnregistered
	 * @return array
	 */
	public function findAllByKeyPid($typeKey, $menuPid = 0, $allowUnregistered = true)
	{
		$rows = $this->getDb()->findAllByKeyPid($typeKey, $menuPid, $allowUnregistered);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $menuId
	 * @return array
	 */
	public function findByPk($menuId)
	{
		$row = $this->getDb()->findByPk($menuId);
		return $row;
	}

	/**
	 * 通过类型Key，查询记录数
	 * @param string $typeKey
	 * @return integer
	 */
	public function countByTypeKey($typeKey)
	{
		$count = $this->getDb()->countByTypeKey($typeKey);
		return $count;
	}

	/**
	 * 批量编辑排序
	 * @param array $params
	 * @return integer
	 */
	public function batchModifySort(array $params = array())
	{
		$rowCount = 0;
		$columnName = 'sort';

		foreach ($params as $pk => $value) {
			$rowCount += $this->modifyByPk($pk, array($columnName => $value));
		}

		return $rowCount;
	}

	/**
	 * 通过“主键ID”，获取“父菜单ID”
	 * @param integer $menuId
	 * @return integer
	 */
	public function getMenuPidByMenuId($menuId)
	{
		$value = $this->getByPk('menu_pid', $menuId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“菜单名”
	 * @param integer $menuId
	 * @return string
	 */
	public function getMenuNameByMenuId($menuId)
	{
		$value = $this->getByPk('menu_name', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“菜单链接”
	 * @param integer $menuId
	 * @return string
	 */
	public function getMenuUrlByMenuId($menuId)
	{
		$value = $this->getByPk('menu_url', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“类型Key”
	 * @param integer $menuId
	 * @return string
	 */
	public function getTypeKeyByMenuId($menuId)
	{
		$value = $this->getByPk('type_key', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“图片链接”
	 * @param integer $menuId
	 * @return string
	 */
	public function getPictureByMenuId($menuId)
	{
		$value = $this->getByPk('picture', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“别名”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAliasByMenuId($menuId)
	{
		$value = $this->getByPk('alias', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $menuId
	 * @return string
	 */
	public function getDescriptionByMenuId($menuId)
	{
		$value = $this->getByPk('description', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“允许非会员访问”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAllowUnregisteredByMenuId($menuId)
	{
		$value = $this->getByPk('allow_unregistered', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否隐藏”
	 * @param integer $menuId
	 * @return string
	 */
	public function getIsHideByMenuId($menuId)
	{
		$value = $this->getByPk('is_hide', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $menuId
	 * @return integer
	 */
	public function getSortByMenuId($menuId)
	{
		$value = $this->getByPk('sort', $menuId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“Target属性”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAttrTargetByMenuId($menuId)
	{
		$value = $this->getByPk('attr_target', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“Title属性”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAttrTitleByMenuId($menuId)
	{
		$value = $this->getByPk('attr_title', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“Rel属性”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAttrRelByMenuId($menuId)
	{
		$value = $this->getByPk('attr_rel', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“Class名”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAttrClassByMenuId($menuId)
	{
		$value = $this->getByPk('attr_class', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“CSS-style属性”
	 * @param integer $menuId
	 * @return string
	 */
	public function getAttrStyleByMenuId($menuId)
	{
		$value = $this->getByPk('attr_style', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $menuId
	 * @return string
	 */
	public function getDtCreatedByMenuId($menuId)
	{
		$value = $this->getByPk('dt_created', $menuId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次编辑时间”
	 * @param integer $menuId
	 * @return string
	 */
	public function getDtLastModifiedByMenuId($menuId)
	{
		$value = $this->getByPk('dt_last_modified', $menuId);
		return $value ? $value : '';
	}

}
