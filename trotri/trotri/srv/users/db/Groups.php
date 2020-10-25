<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\db;

use tdo\AbstractDb;
use users\library\Constant;
use users\library\TableNames;

/**
 * Groups class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Groups.php 1 2014-05-29 18:10:13Z Code Generator $
 * @package users.db
 * @since 1.0
 */
class Groups extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 获取所有的组Id
	 * @return array
	 */
	public function getGroupIds()
	{
		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$sql = 'SELECT `group_id` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchAll($sql);
	}

	/**
	 * 通过父ID，获取所有的组
	 * @param integer $groupPid
	 * @return array
	 */
	public function findAllByPid($groupPid)
	{
		if (($groupPid = (int) $groupPid) < 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$sql = 'SELECT `group_id`, `group_name`, `group_pid`, `sort`, `permission`, `description` FROM `' . $tableName . '` WHERE `group_pid` = ? ORDER BY `sort`';
		return $this->fetchAll($sql, $groupPid);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $groupId
	 * @return array
	 */
	public function findByPk($groupId)
	{
		if (($groupId = (int) $groupId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$sql = 'SELECT `group_id`, `group_name`, `group_pid`, `sort`, `permission`, `description` FROM `' . $tableName . '` WHERE `group_id` = ?';
		return $this->fetchAssoc($sql, $groupId);
	}

	/**
	 * 新增一条记录，禁止新建group_pid=0组
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$groupName = isset($params['group_name']) ? trim($params['group_name']) : '';
		$groupPid = isset($params['group_pid']) ? (int) $params['group_pid'] : 0;
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$description = isset($params['description']) ? $params['description'] : '';

		if ($groupName === '' || $groupPid <= 0 || $sort <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$attributes = array(
			'group_name' => $groupName,
			'group_pid' => $groupPid,
			'sort' => $sort,
			'description' => $description,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录，禁止编辑group_id=1，group_pid=0的根组
	 * @param integer $groupId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($groupId, array $params = array())
	{
		if (($groupId = (int) $groupId) <= 1) {
			return false;
		}

		$attributes = array();

		if (isset($params['group_name'])) {
			$groupName = trim($params['group_name']);
			if ($groupName !== '') {
				$attributes['group_name'] = $groupName;
			}
			else {
				return false;
			}
		}

		if (isset($params['group_pid'])) {
			$groupPid = (int) $params['group_pid'];
			if ($groupPid > 0) {
				$attributes['group_pid'] = $groupPid;
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

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`group_id` = ?');
		$attributes['group_id'] = $groupId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑权限设置
	 * @param integer $groupId
	 * @param string $permission
	 * @return integer
	 */
	public function modifyPermissionByPk($groupId, $permission)
	{
		if (($groupId = (int) $groupId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$sql = 'UPDATE `' . $tableName . '` SET `permission` = ? WHERE `group_id` = ?';
		$attributes['permission'] = $permission;
		$attributes['group_id'] = $groupId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录，禁止删除group_id=1，group_pid=0的根组
	 * @param integer $groupId
	 * @return integer
	 */
	public function removeByPk($groupId)
	{
		if (($groupId = (int) $groupId) <= 1) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getGroups();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`group_id` = ?');
		$rowCount = $this->delete($sql, $groupId);
		return $rowCount;
	}
}
