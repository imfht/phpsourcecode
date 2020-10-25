<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace builders\services;

use builders\library\Lang;

/**
 * DataTypes class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataTypes.php 1 2014-05-26 19:27:28Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class DataTypes
{
	/**
	 * @var string 所属分类：text
	 */
	const CATEGORY_TEXT = 'text';

	/**
	 * @var string 所属分类：option
	 */
	const CATEGORY_OPTION = 'option';

	/**
	 * @var string 所属分类：button
	 */
	const CATEGORY_BUTTON = 'button';

	/**
	 * 获取“所属分类”所有选项
	 * @return array
	 */
	public static function getCategoryEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::CATEGORY_TEXT => Lang::_('SRV_ENUM_BUILDER_TYPES_CATEGORY_TEXT'),
				self::CATEGORY_OPTION => Lang::_('SRV_ENUM_BUILDER_TYPES_CATEGORY_OPTION'),
				self::CATEGORY_BUTTON => Lang::_('SRV_ENUM_BUILDER_TYPES_CATEGORY_BUTTON'),
			);
		}

		return $enum;
	}

}
