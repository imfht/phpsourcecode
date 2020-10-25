<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace advert\services;

use advert\library\Lang;

/**
 * DataAdverts class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataAdverts.php 1 2014-10-26 12:07:53Z Code Generator $
 * @package advert.services
 * @since 1.0
 */
class DataAdverts
{
	/**
	 * @var string 排序字段：sort
	 */
	const ORDER_BY_SORT = 'sort';

	/**
	 * @var string 是否发表：y
	 */
	const IS_PUBLISHED_Y = 'y';

	/**
	 * @var string 是否发表：n
	 */
	const IS_PUBLISHED_N = 'n';

	/**
	 * @var string 展现方式：code
	 */
	const SHOW_TYPE_CODE = 'code';

	/**
	 * @var string 展现方式：text
	 */
	const SHOW_TYPE_TEXT = 'text';

	/**
	 * @var string 展现方式：image
	 */
	const SHOW_TYPE_IMAGE = 'image';

	/**
	 * @var string 展现方式：flash
	 */
	const SHOW_TYPE_FLASH = 'flash';

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
	 * 获取“展现方式”所有选项
	 * @return array
	 */
	public static function getShowTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SHOW_TYPE_CODE => Lang::_('SRV_ENUM_ADVERTS_SHOW_TYPE_CODE'),
				self::SHOW_TYPE_TEXT => Lang::_('SRV_ENUM_ADVERTS_SHOW_TYPE_TEXT'),
				self::SHOW_TYPE_IMAGE => Lang::_('SRV_ENUM_ADVERTS_SHOW_TYPE_IMAGE'),
				self::SHOW_TYPE_FLASH => Lang::_('SRV_ENUM_ADVERTS_SHOW_TYPE_FLASH'),
			);
		}

		return $enum;
	}

}
