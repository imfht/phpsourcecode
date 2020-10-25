<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace menus\db;

use tdo\AbstractDb;
use menus\library\Constant;
use menus\library\TableNames;

/**
 * Menus class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Menus.php 1 2014-10-22 14:27:46Z Code Generator $
 * @package menus.db
 * @since 1.0
 */
class Menus extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 通过“类型Key”和“父菜单ID”，获取所有的菜单
	 * @param string $typeKey
	 * @param integer $menuPid
	 * @param boolean $allowUnregistered
	 * @return array
	 */
	public function findAllByKeyPid($typeKey, $menuPid, $allowUnregistered = true)
	{
		if (($typeKey = trim($typeKey)) === '') {
			return false;
		}

		if (($menuPid = (int) $menuPid) < 0) {
			return false;
		}

		$attributes = array(
			'type_key' => $typeKey,
			'menu_pid' => $menuPid
		);

		if (!$allowUnregistered) {
			$attributes['allow_unregistered'] = 'y';
		}

		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getMenus();
		$sql = 'SELECT `menu_id`, `menu_pid`, `menu_name`, `menu_url`, `type_key`, `picture`, `alias`, `description`, `allow_unregistered`, `is_hide`, `sort`, `attr_target`, `attr_title`, `attr_rel`, `attr_class`, `attr_style`, `dt_created`, `dt_last_modified` FROM `' . $tableName . '`';
		$sql = $commandBuilder->applyCondition($sql, $commandBuilder->createAndCondition(array_keys($attributes)));
		$sql = $commandBuilder->applyOrder($sql, 'sort');
		return $this->fetchAll($sql, $attributes);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $menuId
	 * @return array
	 */
	public function findByPk($menuId)
	{
		if (($menuId = (int) $menuId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMenus();
		$sql = 'SELECT `menu_id`, `menu_pid`, `menu_name`, `menu_url`, `type_key`, `picture`, `alias`, `description`, `allow_unregistered`, `is_hide`, `sort`, `attr_target`, `attr_title`, `attr_rel`, `attr_class`, `attr_style`, `dt_created`, `dt_last_modified` FROM `' . $tableName . '` WHERE `menu_id` = ?';
		return $this->fetchAssoc($sql, $menuId);
	}

	/**
	 * 通过类型Key，查询记录数
	 * @param string $typeKey
	 * @return integer
	 */
	public function countByTypeKey($typeKey)
	{
		if (($typeKey = trim($typeKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMenus();
		$sql = 'SELECT COUNT(*) FROM `' . $tableName . '` WHERE `type_key` = ?';
		return $this->fetchColumn($sql, $typeKey);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$menuPid = isset($params['menu_pid']) ? (int) $params['menu_pid'] : 0;
		$menuName = isset($params['menu_name']) ? trim($params['menu_name']) : '';
		$menuUrl = isset($params['menu_url']) ? trim($params['menu_url']) : '';
		$typeKey = isset($params['type_key']) ? trim($params['type_key']) : '';
		$picture = isset($params['picture']) ? trim($params['picture']) : '';
		$alias = isset($params['alias']) ? trim($params['alias']) : '';
		$description = isset($params['description']) ? $params['description'] : '';
		$allowUnregistered = isset($params['allow_unregistered']) ? trim($params['allow_unregistered']) : '';
		$isHide = isset($params['is_hide']) ? trim($params['is_hide']) : '';
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$attrTarget = isset($params['attr_target']) ? $params['attr_target'] : '';
		$attrTitle = isset($params['attr_title']) ? $params['attr_title'] : '';
		$attrRel = isset($params['attr_rel']) ? $params['attr_rel'] : '';
		$attrClass = isset($params['attr_class']) ? $params['attr_class'] : '';
		$attrStyle = isset($params['attr_style']) ? $params['attr_style'] : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';
		$dtLastModified = isset($params['dt_last_modified']) ? trim($params['dt_last_modified']) : '';

		if ($menuPid < 0 || $menuName === '' || $menuUrl === '' || $typeKey === '' || $sort < 0) {
			return false;
		}

		if ($allowUnregistered === '') {
			$allowUnregistered = 'y';
		}

		if ($isHide === '') {
			$isHide = 'n';
		}

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$dtLastModified = $dtCreated;

		$tableName = $this->getTblprefix() . TableNames::getMenus();
		$attributes = array(
			'menu_pid' => $menuPid,
			'menu_name' => $menuName,
			'menu_url' => $menuUrl,
			'type_key' => $typeKey,
			'picture' => $picture,
			'alias' => $alias,
			'description' => $description,
			'allow_unregistered' => $allowUnregistered,
			'is_hide' => $isHide,
			'sort' => $sort,
			'attr_target' => $attrTarget,
			'attr_title' => $attrTitle,
			'attr_rel' => $attrRel,
			'attr_class' => $attrClass,
			'attr_style' => $attrStyle,
			'dt_created' => $dtCreated,
			'dt_last_modified' => $dtLastModified,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $menuId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($menuId, array $params = array())
	{
		if (($menuId = (int) $menuId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['menu_pid'])) {
			$menuPid = (int) $params['menu_pid'];
			if ($menuPid >= 0) {
				$attributes['menu_pid'] = $menuPid;
			}
			else {
				return false;
			}
		}

		if (isset($params['menu_name'])) {
			$menuName = trim($params['menu_name']);
			if ($menuName !== '') {
				$attributes['menu_name'] = $menuName;
			}
			else {
				return false;
			}
		}

		if (isset($params['menu_url'])) {
			$menuUrl = trim($params['menu_url']);
			if ($menuUrl !== '') {
				$attributes['menu_url'] = $menuUrl;
			}
			else {
				return false;
			}
		}

		if (isset($params['type_key'])) {
			$typeKey = trim($params['type_key']);
			if ($typeKey !== '') {
				$attributes['type_key'] = $typeKey;
			}
			else {
				return false;
			}
		}

		if (isset($params['picture'])) {
			$attributes['picture'] = trim($params['picture']);
		}

		if (isset($params['alias'])) {
			$attributes['alias'] = trim($params['alias']);
		}

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
		}

		if (isset($params['allow_unregistered'])) {
			$allowUnregistered = trim($params['allow_unregistered']);
			if ($allowUnregistered !== '') {
				$attributes['allow_unregistered'] = $allowUnregistered;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_hide'])) {
			$isHide = trim($params['is_hide']);
			if ($isHide !== '') {
				$attributes['is_hide'] = $isHide;
			}
			else {
				return false;
			}
		}

		if (isset($params['sort'])) {
			$sort = (int) $params['sort'];
			if ($sort > 0) {
				$attributes['sort'] = $sort;
			}
			else {
				return false;
			}
		}

		if (isset($params['attr_target'])) {
			$attributes['attr_target'] = $params['attr_target'];
		}

		if (isset($params['attr_title'])) {
			$attributes['attr_title'] = $params['attr_title'];
		}

		if (isset($params['attr_rel'])) {
			$attributes['attr_rel'] = $params['attr_rel'];
		}

		if (isset($params['attr_class'])) {
			$attributes['attr_class'] = $params['attr_class'];
		}

		if (isset($params['attr_style'])) {
			$attributes['attr_style'] = $params['attr_style'];
		}

		if (isset($params['dt_last_modified'])) {
			$dtLastModified = trim($params['dt_last_modified']);
			if ($dtLastModified !== '') {
				$attributes['dt_last_modified'] = $dtLastModified;
			}
			else {
				return false;
			}
		}
		else {
			$params['dt_last_modified'] = date('Y-m-d H:i:s');
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getMenus();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`menu_id` = ?');
		$attributes['menu_id'] = $menuId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $menuId
	 * @return integer
	 */
	public function removeByPk($menuId)
	{
		if (($menuId = (int) $menuId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMenus();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`menu_id` = ?');
		$rowCount = $this->delete($sql, $menuId);
		return $rowCount;
	}
}
