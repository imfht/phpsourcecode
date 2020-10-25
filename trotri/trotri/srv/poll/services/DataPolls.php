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

use poll\library\Lang;

/**
 * DataPolls class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataPolls.php 1 2014-12-05 17:47:10Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class DataPolls
{
	/**
	 * @var string 是否允许非会员参加：y
	 */
	const ALLOW_UNREGISTERED_Y = 'y';

	/**
	 * @var string 是否允许非会员参加：n
	 */
	const ALLOW_UNREGISTERED_N = 'n';

	/**
	 * @var string 参与方式：forever
	 */
	const JOIN_TYPE_FOREVER = 'forever';

	/**
	 * @var string 参与方式：year
	 */
	const JOIN_TYPE_YEAR = 'year';

	/**
	 * @var string 参与方式：month
	 */
	const JOIN_TYPE_MONTH = 'month';

	/**
	 * @var string 参与方式：day
	 */
	const JOIN_TYPE_DAY = 'day';

	/**
	 * @var string 参与方式：hour
	 */
	const JOIN_TYPE_HOUR = 'hour';

	/**
	 * @var string 参与方式：interval
	 */
	const JOIN_TYPE_INTERVAL = 'interval';

	/**
	 * @var string 是否开放：y
	 */
	const IS_PUBLISHED_Y = 'y';

	/**
	 * @var string 是否开放：n
	 */
	const IS_PUBLISHED_N = 'n';

	/**
	 * @var string 是否展示结果：y
	 */
	const IS_VISIBLE_Y = 'y';

	/**
	 * @var string 是否展示结果：n
	 */
	const IS_VISIBLE_N = 'n';

	/**
	 * @var string 是否多选：y
	 */
	const IS_MULTIPLE_Y = 'y';

	/**
	 * @var string 是否多选：n
	 */
	const IS_MULTIPLE_N = 'n';

	/**
	 * 获取“是否允许非会员参加”所有选项
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
	 * 获取“参与方式”所有选项
	 * @return array
	 */
	public static function getJoinTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::JOIN_TYPE_FOREVER => Lang::_('SRV_ENUM_POLLS_JOIN_TYPE_FOREVER'),
				self::JOIN_TYPE_YEAR => Lang::_('SRV_ENUM_POLLS_JOIN_TYPE_YEAR'),
				self::JOIN_TYPE_MONTH => Lang::_('SRV_ENUM_POLLS_JOIN_TYPE_MONTH'),
				self::JOIN_TYPE_DAY => Lang::_('SRV_ENUM_POLLS_JOIN_TYPE_DAY'),
				self::JOIN_TYPE_HOUR => Lang::_('SRV_ENUM_POLLS_JOIN_TYPE_HOUR'),
				self::JOIN_TYPE_INTERVAL => Lang::_('SRV_ENUM_POLLS_JOIN_TYPE_INTERVAL'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否开放”所有选项
	 * @return array
	 */
	public static function getIsPublishedEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUBLISHED_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUBLISHED_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否展示结果”所有选项
	 * @return array
	 */
	public static function getIsVisibleEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_VISIBLE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_VISIBLE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否多选”所有选项
	 * @return array
	 */
	public static function getIsMultipleEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_MULTIPLE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_MULTIPLE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

}
