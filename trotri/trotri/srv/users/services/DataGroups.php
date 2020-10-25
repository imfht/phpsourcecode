<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\services;

use tfc\util\Power;
use users\library\Lang;

/**
 * DataGroups class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataGroups.php 1 2014-05-29 18:56:38Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class DataGroups
{
	/**
	 * @var string 权限：SELECT
	 */
	const POWER_SELECT = Power::MODE_S;

	/**
	 * @var string 权限：INSERT
	 */
	const POWER_INSERT = Power::MODE_I;

	/**
	 * @var string 权限：UPDATE
	 */
	const POWER_UPDATE = Power::MODE_U;

	/**
	 * @var string 权限：DELETE
	 */
	const POWER_DELETE = Power::MODE_D;

	/**
	 * 获取“权限”所有选项
	 * @return array
	 */
	public static function getPowerEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::POWER_SELECT => Lang::_('SRV_ENUM_USER_GROUPS_POWER_SELECT'),
				self::POWER_INSERT => Lang::_('SRV_ENUM_USER_GROUPS_POWER_INSERT'),
				self::POWER_UPDATE => Lang::_('SRV_ENUM_USER_GROUPS_POWER_UPDATE'),
				self::POWER_DELETE => Lang::_('SRV_ENUM_USER_GROUPS_POWER_DELETE'),
			);
		}

		return $enum;
	}
}
