<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\services;

use posts\library\Lang;

/**
 * DataModules class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataModules.php 1 2014-10-12 21:14:11Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class DataModules
{
	/**
	 * @var string 是否禁用：y
	 */
	const FORBIDDEN_Y = 'y';

	/**
	 * @var string 是否禁用：n
	 */
	const FORBIDDEN_N = 'n';

	/**
	 * 获取“是否禁用”所有选项
	 * @return array
	 */
	public static function getForbiddenEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::FORBIDDEN_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::FORBIDDEN_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

}
