<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace menus\services;

use menus\library\Lang;

/**
 * DataMenus class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataMenus.php 1 2014-10-22 14:27:46Z Code Generator $
 * @package menus.services
 * @since 1.0
 */
class DataMenus
{
	/**
	 * @var string 允许非会员访问：y
	 */
	const ALLOW_UNREGISTERED_Y = 'y';

	/**
	 * @var string 允许非会员访问：n
	 */
	const ALLOW_UNREGISTERED_N = 'n';

	/**
	 * @var string 是否隐藏：y
	 */
	const IS_HIDE_Y = 'y';

	/**
	 * @var string 是否隐藏：n
	 */
	const IS_HIDE_N = 'n';

	/**
	 * 获取“允许非会员访问”所有选项
	 * @return array
	 */
	public static function getAllowUnregisteredEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::ALLOW_UNREGISTERED_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::ALLOW_UNREGISTERED_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否隐藏”所有选项
	 * @return array
	 */
	public static function getIsHideEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_HIDE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_HIDE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

}
