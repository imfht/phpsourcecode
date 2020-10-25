<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use libsrv\AbstractService;

/**
 * Ranks class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ranks.php 1 2014-11-26 14:16:14Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class Ranks extends AbstractService
{
	/**
	 * 查询多条记录
	 * @return array
	 */
	public function findAll()
	{
		$rows = $this->getDb()->findAll();
		return $rows;
	}

	/**
	 * 获取所有的成长度Id
	 * @return array
	 */
	public function getRankIds()
	{
		$rows = $this->getDb()->getRankIds();
		return $rows;
	}

	/**
	 * 获取所有的成长度名称
	 * @return array
	 */
	public function getRankNames()
	{
		$rows = $this->getDb()->getRankNames();
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $rankId
	 * @return array
	 */
	public function findByPk($rankId)
	{
		$row = $this->getDb()->findByPk($rankId);
		return $row;
	}

	/**
	 * 通过“主键ID”，获取“成长度名”
	 * @param integer $rankId
	 * @return string
	 */
	public function getRankNameByRankId($rankId)
	{
		$value = $this->getByPk('rank_name', $rankId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“需要成长值”
	 * @param integer $rankId
	 * @return integer
	 */
	public function getExperienceByRankId($rankId)
	{
		$value = $this->getByPk('experience', $rankId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $rankId
	 * @return integer
	 */
	public function getSortByRankId($rankId)
	{
		$value = $this->getByPk('sort', $rankId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $rankId
	 * @return string
	 */
	public function getDescriptionByRankId($rankId)
	{
		$value = $this->getByPk('description', $rankId);
		return $value ? $value : '';
	}

}
