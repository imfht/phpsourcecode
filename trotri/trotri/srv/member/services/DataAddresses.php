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

use member\library\Lang;

/**
 * DataAddresses class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataAddresses.php 1 2014-12-03 17:53:27Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class DataAddresses
{
	/**
	 * @var string 收货最佳时间：anyone
	 */
	const WHEN_ANYONE = 'anyone';

	/**
	 * @var string 收货最佳时间：workday
	 */
	const WHEN_WORKDAY = 'workday';

	/**
	 * @var string 收货最佳时间：weekend
	 */
	const WHEN_WEEKEND = 'weekend';

	/**
	 * @var string 收货最佳时间：holiday
	 */
	const WHEN_HOLIDAY = 'holiday';

	/**
	 * @var string 是否默认地址：y
	 */
	const IS_DEFAULT_Y = 'y';

	/**
	 * @var string 是否默认地址：n
	 */
	const IS_DEFAULT_N = 'n';

	/**
	 * 获取“收货最佳时间”所有选项
	 * @return array
	 */
	public static function getWhenEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::WHEN_ANYONE => Lang::_('SRV_ENUM_MEMBER_ADDRESSES_WHEN_ANYONE'),
				self::WHEN_WORKDAY => Lang::_('SRV_ENUM_MEMBER_ADDRESSES_WHEN_WORKDAY'),
				self::WHEN_WEEKEND => Lang::_('SRV_ENUM_MEMBER_ADDRESSES_WHEN_WEEKEND'),
				self::WHEN_HOLIDAY => Lang::_('SRV_ENUM_MEMBER_ADDRESSES_WHEN_HOLIDAY'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否默认地址”所有选项
	 * @return array
	 */
	public static function getIsDefaultEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_DEFAULT_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_DEFAULT_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

}
