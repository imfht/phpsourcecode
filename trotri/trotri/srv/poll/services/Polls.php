<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace poll\services;

use libsrv\AbstractService;
use poll\library\Constant;

/**
 * Polls class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polls.php 1 2014-12-05 17:47:10Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class Polls extends AbstractService
{
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
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $pollId
	 * @return array
	 */
	public function findByPk($pollId)
	{
		$row = $this->getDb()->findByPk($pollId);
		if ($row && is_array($row) && isset($row['m_rank_ids'])) {
			$mRankIds = $row['m_rank_ids'] ? explode(',', $row['m_rank_ids']) : array();
			$row['m_rank_ids'] = array_map('intval', $mRankIds);
		}

		return $row;
	}

	/**
	 * 通过投票Key，查询一条记录
	 * @param string $pollKey
	 * @param boolean $usable
	 * @return array
	 */
	public function findByPollKey($pollKey, $usable = false)
	{
		$row = $this->getDb()->findByPollKey($pollKey);
		if ($row && is_array($row) && isset($row['m_rank_ids']) && isset($row['poll_id']) && isset($row['is_published']) && isset($row['dt_publish_up']) && isset($row['dt_publish_down'])) {
			if ($usable) {
				if ($row['is_published'] !== DataPolls::IS_PUBLISHED_Y) {
					return array();
				}

				$nowTime = date('Y-m-d H:i:s');
				if ($nowTime < $row['dt_publish_up']) {
					return array();
				}

				if ($row['dt_publish_down'] !== '0000-00-00 00:00:00' && $nowTime > $row['dt_publish_down']) {
					return array();
				}
			}

			$row['allow_unregistered'] = ($row['allow_unregistered'] === DataPolls::ALLOW_UNREGISTERED_Y) ? true : false;
			$row['is_published'] = ($row['is_published'] === DataPolls::IS_PUBLISHED_Y) ? true : false;
			$row['is_visible'] = ($row['is_visible'] === DataPolls::IS_VISIBLE_Y) ? true : false;
			$row['is_multiple'] = ($row['is_multiple'] === DataPolls::IS_MULTIPLE_Y) ? true : false;

			$mRankIds = $row['m_rank_ids'] ? explode(',', $row['m_rank_ids']) : array();
			$row['m_rank_ids'] = array_map('intval', $mRankIds);

			return $row;
		}

		return false;
	}

	/**
	 * 通过“主键ID”，获取“投票名”
	 * @param integer $pollId
	 * @return string
	 */
	public function getPollNameByPollId($pollId)
	{
		$value = $this->getByPk('poll_name', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“投票Key”
	 * @param integer $pollId
	 * @return string
	 */
	public function getPollKeyByPollId($pollId)
	{
		$value = $this->getByPk('poll_key', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否允许非会员参加”
	 * @param integer $pollId
	 * @return string
	 */
	public function getAllowUnregisteredByPollId($pollId)
	{
		$value = $this->getByPk('allow_unregistered', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“允许参与会员成长度”
	 * @param integer $pollId
	 * @return string
	 */
	public function getMRankIdsByPollId($pollId)
	{
		$value = $this->getByPk('m_rank_ids', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“参与方式”
	 * @param integer $pollId
	 * @return string
	 */
	public function getJoinTypeByPollId($pollId)
	{
		$value = $this->getByPk('join_type', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“间隔几秒可再次参与”
	 * @param integer $pollId
	 * @return integer
	 */
	public function getIntervalByPollId($pollId)
	{
		$value = $this->getByPk('interval', $pollId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“是否开放”
	 * @param integer $pollId
	 * @return string
	 */
	public function getIsPublishedByPollId($pollId)
	{
		$value = $this->getByPk('is_published', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“开始时间”
	 * @param integer $pollId
	 * @return string
	 */
	public function getDtPublishUpByPollId($pollId)
	{
		$value = $this->getByPk('dt_publish_up', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“结束时间”
	 * @param integer $pollId
	 * @return string
	 */
	public function getDtPublishDownByPollId($pollId)
	{
		$value = $this->getByPk('dt_publish_down', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否展示结果”
	 * @param integer $pollId
	 * @return string
	 */
	public function getIsVisibleByPollId($pollId)
	{
		$value = $this->getByPk('is_visible', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否多选”
	 * @param integer $pollId
	 * @return string
	 */
	public function getIsMultipleByPollId($pollId)
	{
		$value = $this->getByPk('is_multiple', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“最多可选数量”
	 * @param integer $pollId
	 * @return integer
	 */
	public function getMaxChoicesByPollId($pollId)
	{
		$value = $this->getByPk('max_choices', $pollId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $pollId
	 * @return string
	 */
	public function getDescriptionByPollId($pollId)
	{
		$value = $this->getByPk('description', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“扩展属性”
	 * @param integer $pollId
	 * @return string
	 */
	public function getExtInfoByPollId($pollId)
	{
		$value = $this->getByPk('ext_info', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $pollId
	 * @return string
	 */
	public function getDtCreatedByPollId($pollId)
	{
		$value = $this->getByPk('dt_created', $pollId);
		return $value ? $value : '';
	}

	/**
	 * 获取“参与方式”
	 * @param string $joinType
	 * @return string
	 */
	public function getJoinTypeLangByJoinType($joinType)
	{
		$enum = DataPolls::getJoinTypeEnum();
		return isset($enum[$joinType]) ? $enum[$joinType] : '';
	}

	/**
	 * 获取“是否展示结果”
	 * @param string $isVisible
	 * @return string
	 */
	public function getIsVisibleLangByIsVisible($isVisible)
	{
		$enum = DataPolls::getIsVisibleEnum();
		return isset($enum[$isVisible]) ? $enum[$isVisible] : '';
	}

	/**
	 * 获取“是否多选”
	 * @param string $isMultiple
	 * @return string
	 */
	public function getIsMultipleLangByIsMultiple($isMultiple)
	{
		$enum = DataPolls::getIsMultipleEnum();
		return isset($enum[$isMultiple]) ? $enum[$isMultiple] : '';
	}

	/**
	 * 获取“是否允许非会员参加”
	 * @param string $allowUnregistered
	 * @return string
	 */
	public function getAllowUnregisteredByAllowUnregistered($allowUnregistered)
	{
		$enum = DataPolls::getAllowUnregisteredEnum();
		return isset($enum[$allowUnregistered]) ? $enum[$allowUnregistered] : '';
	}

}
