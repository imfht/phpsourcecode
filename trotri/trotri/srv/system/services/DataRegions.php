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

use system\library\Lang;

/**
 * DataRegions class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataRegions.php 1 2014-08-19 00:15:56Z Code Generator $
 * @package system.services
 * @since 1.0
 */
class DataRegions
{
	/**
	 * @var string 地区类型：国家
	 */
	CONST REGION_TYPE_0 = 0;

	/**
	 * @VAR string 地区类型：省
	 */
	CONST REGION_TYPE_1 = 1;

	/**
	 * @VAR string 地区类型：城市
	 */
	CONST REGION_TYPE_2 = 2;

	/**
	 * @VAR string 地区类型：区域
	 */
	CONST REGION_TYPE_3 = 3;

	/**
	 * 获取“地区类型”所有选项
	 * @return array
	 */
	public static function getRegionTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::REGION_TYPE_0 => Lang::_('SRV_ENUM_SYSTEM_REGIONS_REGION_TYPE_0'),
				self::REGION_TYPE_1 => Lang::_('SRV_ENUM_SYSTEM_REGIONS_REGION_TYPE_1'),
				self::REGION_TYPE_2 => Lang::_('SRV_ENUM_SYSTEM_REGIONS_REGION_TYPE_2'),
				self::REGION_TYPE_3 => Lang::_('SRV_ENUM_SYSTEM_REGIONS_REGION_TYPE_3'),
			);
		}

		return $enum;
	}

}
