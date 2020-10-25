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
 * DataComments class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataComments.php 1 2014-10-31 11:14:54Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class DataComments
{
	/**
	 * @var string 排序字段：dt_last_modified
	 */
	const ORDER_BY_DT_LAST_MODIFIED = 'dt_last_modified DESC';

	/**
	 * @var string 排序字段：good_count
	 */
	const ORDER_BY_GOOD_COUNT = 'good_count DESC';

	/**
	 * @var string 是否发表：y
	 */
	const IS_PUBLISHED_Y = 'y';

	/**
	 * @var string 是否发表：n
	 */
	const IS_PUBLISHED_N = 'n';

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

}
