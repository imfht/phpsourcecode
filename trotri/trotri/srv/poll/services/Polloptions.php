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

/**
 * Polloptions class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polloptions.php 1 2014-12-06 21:49:14Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class Polloptions extends AbstractService
{
	/**
	 * 通过“投票ID”，获取所有的选项
	 * @param integer $pollId
	 * @return array
	 */
	public function findAllByPollId($pollId)
	{
		$rows = $this->getDb()->findAllByPollId($pollId);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $optionId
	 * @return array
	 */
	public function findByPk($optionId)
	{
		$row = $this->getDb()->findByPk($optionId);
		return $row;
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
	 * 通过“主键ID”，获取“选项”
	 * @param integer $optionId
	 * @return string
	 */
	public function getOptionNameByOptionId($optionId)
	{
		$value = $this->getByPk('option_name', $optionId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“投票名”
	 * @param integer $optionId
	 * @return integer
	 */
	public function getPollIdByOptionId($optionId)
	{
		$value = $this->getByPk('poll_id', $optionId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“票数”
	 * @param integer $optionId
	 * @return integer
	 */
	public function getVotesByOptionId($optionId)
	{
		$value = $this->getByPk('votes', $optionId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $optionId
	 * @return integer
	 */
	public function getSortByOptionId($optionId)
	{
		$value = $this->getByPk('sort', $optionId);
		return $value ? (int) $value : 0;
	}

}
