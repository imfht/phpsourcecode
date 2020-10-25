<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\db;

use tdo\AbstractDb;
use member\library\Constant;
use member\library\TableNames;

/**
 * Ranks class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ranks.php 1 2014-11-26 11:36:19Z Code Generator $
 * @package member.db
 * @since 1.0
 */
class Ranks extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 查询多条记录
	 * @return array
	 */
	public function findAll()
	{
		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$sql = 'SELECT `rank_id`, `rank_name`, `experience`, `sort`, `description` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchAll($sql);
	}

	/**
	 * 获取所有的成长度Id
	 * @return array
	 */
	public function getRankIds()
	{
		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$sql = 'SELECT `rank_id` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchAll($sql);
	}

	/**
	 * 获取所有的成长度名称
	 * @return array
	 */
	public function getRankNames()
	{
		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$sql = 'SELECT `rank_id`, `rank_name` FROM `' . $tableName . '` ORDER BY `sort`';
		return $this->fetchPairs($sql);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $rankId
	 * @return array
	 */
	public function findByPk($rankId)
	{
		if (($rankId = (int) $rankId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$sql = 'SELECT `rank_id`, `rank_name`, `experience`, `sort`, `description` FROM `' . $tableName . '` WHERE `rank_id` = ?';
		return $this->fetchAssoc($sql, $rankId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$rankName = isset($params['rank_name']) ? trim($params['rank_name']) : '';
		$experience = isset($params['experience']) ? (int) $params['experience'] : 0;
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;
		$description = isset($params['description']) ? trim($params['description']) : '';

		if ($rankName === '' || $experience < 0 || $sort <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$attributes = array(
			'rank_name' => $rankName,
			'experience' => $experience,
			'sort' => $sort,
			'description' => $description,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $rankId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($rankId, array $params = array())
	{
		if (($rankId = (int) $rankId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['rank_name'])) {
			$rankName = trim($params['rank_name']);
			if ($rankName !== '') {
				$attributes['rank_name'] = $rankName;
			}
			else {
				return false;
			}
		}

		if (isset($params['experience'])) {
			$experience = (int) $params['experience'];
			if ($experience >= 0) {
				$attributes['experience'] = $experience;
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

		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`rank_id` = ?');
		$attributes['rank_id'] = $rankId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $rankId
	 * @return integer
	 */
	public function removeByPk($rankId)
	{
		if (($rankId = (int) $rankId) <= 1) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getRanks();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`rank_id` = ?');
		$rowCount = $this->delete($sql, $rankId);
		return $rowCount;
	}
}
