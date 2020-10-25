<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace system\services;

use libsrv\AbstractService;

/**
 * Regions class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Regions.php 1 2014-12-01 16:15:48Z Code Generator $
 * @package system.services
 * @since 1.0
 */
class Regions extends AbstractService
{
	/**
	 * 通过父ID，获取所有的地区
	 * @param integer $regionPid
	 * @param integer $regionType
	 * @return array
	 */
	public function findPairs($regionPid, $regionType = -1)
	{
		$rows = $this->getDb()->findPairs($regionPid, $regionType);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $regionId
	 * @return array
	 */
	public function findByPk($regionId)
	{
		$row = $this->getDb()->findByPk($regionId);
		return $row;
	}

	/**
	 * 通过“主键ID”，获取“父ID”
	 * @param integer $regionId
	 * @return integer
	 */
	public function getRegionPidByRegionId($regionId)
	{
		$value = $this->getByPk('region_pid', $regionId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“地区名”
	 * @param integer $regionId
	 * @return string
	 */
	public function getRegionNameByRegionId($regionId)
	{
		$value = $this->getByPk('region_name', $regionId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“地区类型”
	 * @param integer $regionId
	 * @return string
	 */
	public function getRegionTypeByRegionId($regionId)
	{
		$value = $this->getByPk('region_type', $regionId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“地区类型”
	 * @param string $regionType
	 * @return string
	 */
	public function getRegionTypeLangByRegionType($regionType)
	{
		$enum = DataRegions::getRegionTypeEnum();
		return isset($enum[$regionType]) ? $enum[$regionType] : '';
	}

}
