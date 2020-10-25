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
 * Usergroups class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Usergroups.php 1 2014-08-06 15:36:21Z Code Generator $
 * @package users.db
 * @since 1.0
 */
class Usergroups extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 通过用户ID，获取该用户所属的组ID
	 * @param integer $userId
	 * @return array
	 */
	public function findGroupIdsByUserId($userId)
	{
		if (($userId = (int) $userId) < 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getUsergroups();
		$sql = 'SELECT `group_id` FROM `' . $tableName . '` WHERE `user_id` = ?';
		return $this->fetchAll($sql, $userId);
	}

	/**
	 * 新增一条记录
	 * @param integer $userId
	 * @param integer $groupId
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create($userId, $groupId, $ignore = false)
	{
		if (($userId = (int) $userId) <= 0) {
			return false;
		}

		if (($groupId = (int) $groupId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getUsergroups();
		$attributes = array(
			'user_id' => $userId,
			'group_id' => $groupId,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 新增多条记录
	 * @param integer $userId
	 * @param array $groupIds
	 * @param boolean $ignore
	 * @return integer
	 */
	public function batchCreate($userId, $groupIds, $ignore = false)
	{
		$rowCount = 0;

		$groupIds = (array) $groupIds;
		foreach ($groupIds as $groupId) {
			if (($value = $this->create($userId, $groupId, $ignore)) !== false) {
				$rowCount++;
			}
		}

		return $rowCount;
	}

	/**
	 * 删除一条记录
	 * @param integer $userId
	 * @param integer $groupId
	 * @return integer
	 */
	public function remove($userId, $groupId)
	{
		if (($userId = (int) $userId) <= 0) {
			return false;
		}

		if (($groupId = (int) $groupId) <= 0) {
			return false;
		}

		$pks = array(
			'user_id' => $userId,
			'group_id' => $groupId,
		);

		$tableName = $this->getTblprefix() . TableNames::getUsergroups();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`user_id` = ? AND `group_id` = ?');
		$rowCount = $this->delete($sql, $pks);
		return $rowCount;
	}

	/**
	 * 删除多条记录
	 * @param integer $userId
	 * @param array $groupIds
	 * @return integer
	 */
	public function batchRemove($userId, $groupIds)
	{
		$rowCount = 0;

		$groupIds = (array) $groupIds;
		foreach ($groupIds as $groupId) {
			if (($value = $this->remove($userId, $groupId)) !== false) {
				$rowCount += $value;
			}
		}

		return $rowCount;
	}
}
