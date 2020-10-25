<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace topic\services;

use topic\library\Lang;

/**
 * DataTopic class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataTopic.php 1 2014-11-04 16:50:14Z Code Generator $
 * @package topic.services
 * @since 1.0
 */
class DataTopic
{
	/**
	 * @var string 默认排序字段
	 */
	const ORDER_BY_SORT = 'sort ASC, dt_created DESC';
	
	/**
	 * @var string 是否发表：y
	 */
	const IS_PUBLISHED_Y = 'y';

	/**
	 * @var string 是否发表：n
	 */
	const IS_PUBLISHED_N = 'n';

	/**
	 * @var string 使用公共的页头：y
	 */
	const USE_HEADER_Y = 'y';

	/**
	 * @var string 使用公共的页头：n
	 */
	const USE_HEADER_N = 'n';

	/**
	 * @var string 使用公共的页脚：y
	 */
	const USE_FOOTER_Y = 'y';

	/**
	 * @var string 使用公共的页脚：n
	 */
	const USE_FOOTER_N = 'n';

	/**
	 * 获取“是否发表”所有选项
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
	 * 获取“使用公共的页头”所有选项
	 * @return array
	 */
	public static function getUseHeaderEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::USE_HEADER_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::USE_HEADER_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“使用公共的页脚”所有选项
	 * @return array
	 */
	public static function getUseFooterEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::USE_FOOTER_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::USE_FOOTER_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

}
