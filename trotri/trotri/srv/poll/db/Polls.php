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
 * Polls class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polls.php 1 2014-12-05 17:47:10Z Code Generator $
 * @package poll.db
 * @since 1.0
 */
class Polls extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getPolls();
		$sql = 'SELECT ' . $option . ' `poll_id`, `poll_name`, `poll_key`, `allow_unregistered`, `m_rank_ids`, `join_type`, `interval`, `is_published`, `dt_publish_up`, `dt_publish_down`, `is_visible`, `is_multiple`, `max_choices`, `description`, `ext_info`, `dt_created` FROM `' . $tableName . '`';

		$condition = '1';
		$attributes = array();

		if (isset($params['poll_name'])) {
			$pollName = trim($params['poll_name']);
			if ($pollName !== '') {
				$condition .= ' AND `poll_name` LIKE ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['poll_name'] = '%' . $pollName . '%';
			}
		}

		if (isset($params['poll_key'])) {
			$pollKey = trim($params['poll_key']);
			if ($pollKey !== '') {
				$condition .= ' AND `poll_key` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['poll_key'] = $pollKey;
			}
		}

		if (isset($params['allow_unregistered'])) {
			$allowUnregistered = trim($params['allow_unregistered']);
			if ($allowUnregistered !== '') {
				$condition .= ' AND `allow_unregistered` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['allow_unregistered'] = $allowUnregistered;
			}
		}

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$condition .= ' AND `is_published` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['is_published'] = $isPublished;
			}
		}

		if (isset($params['poll_id'])) {
			$pollId = (int) $params['poll_id'];
			if ($pollId > 0) {
				$condition .= ' AND `poll_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['poll_id'] = $pollId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (isset($attributes['poll_name'])) {
				$attributes['poll_name'] = $pollName;
			}
			if (is_array($ret)) {
				$ret['attributes'] = $attributes;
				$ret['order']      = $order;
				$ret['limit']      = $limit;
				$ret['offset']     = $offset;
			}
		}
		else {
			$ret = $this->fetchAll($sql, $attributes);
		}

		return $ret;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $pollId
	 * @return array
	 */
	public function findByPk($pollId)
	{
		if (($pollId = (int) $pollId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolls();
		$sql = 'SELECT `poll_id`, `poll_name`, `poll_key`, `allow_unregistered`, `m_rank_ids`, `join_type`, `interval`, `is_published`, `dt_publish_up`, `dt_publish_down`, `is_visible`, `is_multiple`, `max_choices`, `description`, `ext_info`, `dt_created` FROM `' . $tableName . '` WHERE `poll_id` = ?';
		return $this->fetchAssoc($sql, $pollId);
	}

	/**
	 * 通过投票Key，查询一条记录
	 * @param string $pollKey
	 * @return array
	 */
	public function findByPollKey($pollKey)
	{
		if (($pollKey = trim($pollKey)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolls();
		$sql = 'SELECT `poll_id`, `poll_name`, `poll_key`, `allow_unregistered`, `m_rank_ids`, `join_type`, `interval`, `is_published`, `dt_publish_up`, `dt_publish_down`, `is_visible`, `is_multiple`, `max_choices`, `description`, `ext_info`, `dt_created` FROM `' . $tableName . '` WHERE `poll_key` = ?';
		return $this->fetchAssoc($sql, $pollKey);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$pollName = isset($params['poll_name']) ? trim($params['poll_name']) : '';
		$pollKey = isset($params['poll_key']) ? trim($params['poll_key']) : '';
		$allowUnregistered = isset($params['allow_unregistered']) ? trim($params['allow_unregistered']) : '';
		$mRankIds = isset($params['m_rank_ids']) ? trim($params['m_rank_ids']) : '';
		$joinType = isset($params['join_type']) ? trim($params['join_type']) : '';
		$interval = isset($params['interval']) ? (int) $params['interval'] : 0;
		$isPublished = isset($params['is_published']) ? trim($params['is_published']) : '';
		$dtPublishUp = isset($params['dt_publish_up']) ? trim($params['dt_publish_up']) : '';
		$dtPublishDown = isset($params['dt_publish_down']) ? trim($params['dt_publish_down']) : '';
		$isVisible = isset($params['is_visible']) ? trim($params['is_visible']) : '';
		$isMultiple = isset($params['is_multiple']) ? trim($params['is_multiple']) : '';
		$maxChoices = isset($params['max_choices']) ? (int) $params['max_choices'] : 0;
		$description = isset($params['description']) ? trim($params['description']) : '';
		$extInfo = isset($params['ext_info']) ? trim($params['ext_info']) : '';
		$dtCreated = isset($params['dt_created']) ? trim($params['dt_created']) : '';

		if ($pollName === '' || $pollKey === '' || $allowUnregistered === '' || $joinType === '') {
			return false;
		}

		if ($interval < 0) {
			$interval = 0;
		}

		if ($isPublished === '') {
			$isPublished = 'n';
		}

		if ($dtPublishUp === '') {
			$dtPublishUp = date('Y-m-d H:i:s');
		}

		if ($dtPublishDown === '') {
			$dtPublishDown = '0000-00-00 00:00:00';
		}

		if ($isVisible === '') {
			$isVisible = 'y';
		}

		if ($isMultiple === '') {
			$isMultiple = 'y';
		}

		if ($maxChoices < 0) {
			$maxChoices = 0;
		}

		if ($dtCreated === '') {
			$dtCreated = date('Y-m-d H:i:s');
		}

		$tableName = $this->getTblprefix() . TableNames::getPolls();
		$attributes = array(
			'poll_name' => $pollName,
			'poll_key' => $pollKey,
			'allow_unregistered' => $allowUnregistered,
			'm_rank_ids' => $mRankIds,
			'join_type' => $joinType,
			'interval' => $interval,
			'is_published' => $isPublished,
			'dt_publish_up' => $dtPublishUp,
			'dt_publish_down' => $dtPublishDown,
			'is_visible' => $isVisible,
			'is_multiple' => $isMultiple,
			'max_choices' => $maxChoices,
			'description' => $description,
			'ext_info' => $extInfo,
			'dt_created' => $dtCreated,
		);

		$sql = $this->getCommandBuilder()->createInsert($tableName, array_keys($attributes), $ignore);
		$lastInsertId = $this->insert($sql, $attributes);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $pollId
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($pollId, array $params = array())
	{
		if (($pollId = (int) $pollId) <= 0) {
			return false;
		}

		$attributes = array();

		if (isset($params['poll_name'])) {
			$pollName = trim($params['poll_name']);
			if ($pollName !== '') {
				$attributes['poll_name'] = $pollName;
			}
			else {
				return false;
			}
		}

		if (isset($params['poll_key'])) {
			$pollKey = trim($params['poll_key']);
			if ($pollKey !== '') {
				$attributes['poll_key'] = $pollKey;
			}
			else {
				return false;
			}
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

		if (isset($params['m_rank_ids'])) {
			$attributes['m_rank_ids'] = trim($params['m_rank_ids']);
		}

		if (isset($params['join_type'])) {
			$joinType = trim($params['join_type']);
			if ($joinType !== '') {
				$attributes['join_type'] = $joinType;
			}
			else {
				return false;
			}
		}

		if (isset($params['interval'])) {
			$interval = (int) $params['interval'];
			if ($interval >= 0) {
				$attributes['interval'] = $interval;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_published'])) {
			$isPublished = trim($params['is_published']);
			if ($isPublished !== '') {
				$attributes['is_published'] = $isPublished;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_publish_up'])) {
			$dtPublishUp = trim($params['dt_publish_up']);
			if ($dtPublishUp !== '') {
				$attributes['dt_publish_up'] = $dtPublishUp;
			}
			else {
				return false;
			}
		}

		if (isset($params['dt_publish_down'])) {
			$dtPublishDown = trim($params['dt_publish_down']);
			if ($dtPublishDown !== '') {
				$attributes['dt_publish_down'] = $dtPublishDown;
			}
			else {
				$attributes['dt_publish_down'] = '0000-00-00 00:00:00';
			}
		}

		if (isset($params['is_visible'])) {
			$isVisible = trim($params['is_visible']);
			if ($isVisible !== '') {
				$attributes['is_visible'] = $isVisible;
			}
			else {
				return false;
			}
		}

		if (isset($params['is_multiple'])) {
			$isMultiple = trim($params['is_multiple']);
			if ($isMultiple !== '') {
				$attributes['is_multiple'] = $isMultiple;
			}
			else {
				return false;
			}
		}

		if (isset($params['max_choices'])) {
			$maxChoices = (int) $params['max_choices'];
			if ($maxChoices >= 0) {
				$attributes['max_choices'] = $maxChoices;
			}
			else {
				return false;
			}
		}

		if (isset($params['description'])) {
			$attributes['description'] = $params['description'];
		}

		if (isset($params['ext_info'])) {
			$attributes['ext_info'] = $params['ext_info'];
		}

		$rowCount = 0;

		if ($attributes === array()) {
			return $rowCount;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolls();
		$sql = $this->getCommandBuilder()->createUpdate($tableName, array_keys($attributes), '`poll_id` = ?');
		$attributes['poll_id'] = $pollId;
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $pollId
	 * @return integer
	 */
	public function removeByPk($pollId)
	{
		if (($pollId = (int) $pollId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getPolls();
		$sql = $this->getCommandBuilder()->createDelete($tableName, '`poll_id` = ?');
		$rowCount = $this->delete($sql, $pollId);
		return $rowCount;
	}
}
