<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace system\db;

use tdo\AbstractDb;
use system\library\Constant;
use system\library\TableNames;

/**
 * Regions class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Regions.php 1 2014-12-01 16:15:48Z Code Generator $
 * @package system.db
 * @since 1.0
 */
class Regions extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

	/**
	 * 通过父ID，获取所有的地区
	 * @param integer $regionPid
	 * @param integer $regionType
	 * @return array
	 */
	public function findPairs($regionPid, $regionType = -1)
	{
		if (($regionPid = (int) $regionPid) < 0) {
			return false;
		}

		$commandBuilder = $this->getCommandBuilder();
		$tableName = $this->getTblprefix() . TableNames::getRegions();
		$sql = 'SELECT `region_id`, `region_name` FROM `' . $tableName . '`';

		$condition = '`region_pid` = ?';
		$attributes = array('region_pid' => $regionPid);

		if (($regionType = (int) $regionType) >= 0) {
			$condition .= ' AND `region_type` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['region_type'] = $regionType;
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		return $this->fetchPairs($sql, $attributes);
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $regionId
	 * @return array
	 */
	public function findByPk($regionId)
	{
		if (($regionId = (int) $regionId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getRegions();
		$sql = 'SELECT `region_id`, `region_pid`, `region_name`, `region_type` FROM `' . $tableName . '` WHERE `region_id` = ?';
		return $this->fetchAssoc($sql, $regionId);
	}

}
