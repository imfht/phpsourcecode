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
 * DataTypes class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataTypes.php 1 2014-10-23 23:30:01Z Code Generator $
 * @package advert.services
 * @since 1.0
 */
class DataTypes
{
	/**
	 * @var string 示例图片：header
	 */
	const PICTURE_HEADER = 'header';

	/**
	 * @var string 示例图片：footer
	 */
	const PICTURE_FOOTER = 'footer';

	/**
	 * @var string 示例图片：banner
	 */
	const PICTURE_BANNER = 'banner';

	/**
	 * @var string 示例图片：banner_higher
	 */
	const PICTURE_BANNER_HIGHER = 'banner_higher';

	/**
	 * @var string 示例图片：navbar
	 */
	const PICTURE_NAVBAR = 'navbar';

	/**
	 * @var string 示例图片：navs
	 */
	const PICTURE_NAVS = 'navs';

	/**
	 * @var string 示例图片：sides
	 */
	const PICTURE_SIDES = 'sides';

	/**
	 * @var string 示例图片：notice
	 */
	const PICTURE_NOTICE = 'notice';

	/**
	 * @var string 示例图片：block
	 */
	const PICTURE_BLOCK = 'block';

	/**
	 * @var string 示例图片：block_float
	 */
	const PICTURE_BLOCK_FLOAT = 'block_float';

	/**
	 * @var string 示例图片：list
	 */
	const PICTURE_LIST = 'list';

	/**
	 * @var string 示例图片：list_higher
	 */
	const PICTURE_LIST_HIGHER = 'list_higher';

	/**
	 * @var string 示例图片：list_side
	 */
	const PICTURE_LIST_SIDE = 'list_side';

	/**
	 * @var string 示例图片：views
	 */
	const PICTURE_VIEWS = 'views';

	/**
	 * @var string 示例图片：view_left
	 */
	const PICTURE_VIEW_LEFT = 'view_left';

	/**
	 * @var string 示例图片：view_right
	 */
	const PICTURE_VIEW_RIGHT = 'view_right';

	/**
	 * @var string 示例图片：default
	 */
	const PICTURE_DEFAULT = 'default';

	/**
	 * 获取“示例图片”所有选项
	 * @return array
	 */
	public static function getPictureEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::PICTURE_HEADER => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_HEADER'),
				self::PICTURE_FOOTER => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_FOOTER'),
				self::PICTURE_BANNER => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_BANNER'),
				self::PICTURE_BANNER_HIGHER => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_BANNER_HIGHER'),
				self::PICTURE_NAVBAR => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_NAVBAR'),
				self::PICTURE_NAVS => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_NAVS'),
				self::PICTURE_SIDES => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_SIDES'),
				self::PICTURE_NOTICE => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_NOTICE'),
				self::PICTURE_BLOCK => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_BLOCK'),
				self::PICTURE_BLOCK_FLOAT => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_BLOCK_FLOAT'),
				self::PICTURE_LIST => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_LIST'),
				self::PICTURE_LIST_HIGHER => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_LIST_HIGHER'),
				self::PICTURE_LIST_SIDE => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_LIST_SIDE'),
				self::PICTURE_VIEWS => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_VIEWS'),
				self::PICTURE_VIEW_LEFT => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_VIEW_LEFT'),
				self::PICTURE_VIEW_RIGHT => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_VIEW_RIGHT'),
				self::PICTURE_DEFAULT => Lang::_('SRV_ENUM_ADVERT_TYPES_PICTURE_DEFAULT'),
			);
		}

		return $enum;
	}

}
