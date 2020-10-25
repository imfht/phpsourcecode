<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace poll\db;

use tdo\AbstractDb;
use poll\library\Constant;
use poll\library\TableNames;

/**
 * Polloptions class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polloptions.php 1 2014-12-06 21:49:14Z Code Generator $
 * @package poll.db
 * @since 1.0
 */
class Polloptions extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 通过“投票ID”，获取所有的选项
	 * @param integer $pollId
	 * @return array
	 */
	public function findAllByPollId($pollId)
	{
		if (($pollId = (int) $pollId) < 0) {
			return false;
		}

		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getPolloptions();
		$sql = 'SELECT `option_id`, `option_name`, `poll_id`, `votes`, `sort` FROM `' . $tableName . '` WHERE `poll_id` = ?';
		$sql = $commandBuilder->applyOrder($sql, 'sort ASC');
		return $this->fetchAll($sql, $pollId);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $optionId
	 * @return array
	 */
	public function findByPk($optionId)
	{
		if (($optionId = (int) $optionId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolloptions();
		$sql = 'SELECT `option_id`, `option_name`, `poll_id`, `votes`, `sort` FROM `' . $tableName . '` WHERE `option_id` = ?';
		return $this->fetchAssoc($sql, $optionId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$optionName = isset($params['option_name']) ? trim($params['option_name']) : '';
		$pollId = isset($params['poll_id']) ? (int) $params['poll_id'] : 0;
		$votes = isset($params['votes']) ? (int) $params['votes'] : 0;
		$sort = isset($params['sort']) ? (int) $params['sort'] : 0;

		if ($optionName === '' || $pollId <= 0 || $votes < 0 || $sort <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolloptions();
		$attributes = array(
			'option_name' => $optionName,
			'poll_id' => $pollId,
			'votes' => $votes,
			'sort' => $sort,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $optionId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($optionId, array $params = array())
	{
		if (($optionId = (int) $optionId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['option_name'])) {
			$optionName = trim($params['option_name']);
			if ($optionName !== '') {
				$attributes['option_name'] = $optionName;
			}
			else {
				return false;
			}
		}

		if (isset($params['votes'])) {
			$votes = (int) $params['votes'];
			if ($votes >= 0) {
				$attributes['votes'] = $votes;
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

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolloptions();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`option_id` = ?');
		$attributes['option_id'] = $optionId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $optionId
	 * @return integer
	 */
	public function removeByPk($optionId)
	{
		if (($optionId = (int) $optionId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolloptions();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`option_id` = ?');
		$rowCount = $this->delete($sql, $optionId);
		return $rowCount;
	}
}
