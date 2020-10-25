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
 * DataValidators class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataValidators.php 1 2014-05-28 11:06:31Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class DataValidators
{
	/**
	 * @var string 验证时对比值类型：boolean
	 */
	const OPTION_CATEGORY_BOOLEAN = 'boolean';

	/**
	 * @var string 验证时对比值类型：integer
	 */
	const OPTION_CATEGORY_INTEGER = 'integer';

	/**
	 * @var string 验证时对比值类型：string
	 */
	const OPTION_CATEGORY_STRING = 'string';

	/**
	 * @var string 验证时对比值类型：array
	 */
	const OPTION_CATEGORY_ARRAY = 'array';

	/**
	 * @var string 验证环境：all
	 */
	const WHEN_ALL = 'all';

	/**
	 * @var string 验证环境：create
	 */
	const WHEN_CREATE = 'create';

	/**
	 * @var string 验证环境：modify
	 */
	const WHEN_MODIFY = 'modify';

	/**
	 * 获取“验证时对比值类型”所有选项
	 * @return array
	 */
	public static function getOptionCategoryEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::OPTION_CATEGORY_BOOLEAN => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_BOOLEAN'),
				self::OPTION_CATEGORY_INTEGER => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_INTEGER'),
				self::OPTION_CATEGORY_STRING => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_STRING'),
				self::OPTION_CATEGORY_ARRAY => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_OPTION_CATEGORY_ARRAY'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“验证环境”所有选项
	 * @return array
	 */
	public static function getWhenEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::WHEN_ALL => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_WHEN_ALL'),
				self::WHEN_CREATE => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_WHEN_CREATE'),
				self::WHEN_MODIFY => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_WHEN_MODIFY'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“验证类名”所有选项
	 * @return array
	 */
	public static function getValidatorNameEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				'AlphaNum' => 'AlphaNum',
				'Alpha' => 'Alpha',
				'EqualTo' => 'EqualTo',
				'Equal' => 'Equal',
				'Float' => 'Float',
				'InArray' => 'InArray',
				'Integer' => 'Integer',
				'Ip' => 'Ip',
				'Mail' => 'Mail',
				'MaxLength' => 'MaxLength',
				'Max' => 'Max',
				'MinLength' => 'MinLength',
				'Min' => 'Min',
				'NotEmpty' => 'NotEmpty',
				'Numeric' => 'Numeric',
				'Require' => 'Require',
				'Url' => 'Url',
				'DateTime' => 'DateTime',
				'NonNegativeInteger' => 'NonNegativeInteger',
			);
		}

		return $enum;
	}

	/**
	 * 获取“出错提示消息”所有选项
	 * @return array
	 */
	public static function getMessageEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				'AlphaNum' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_ALPHANUM'),
				),
				'Alpha' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_ALPHA'),
				),
				'EqualTo' => array(
					'option_category' => 'string',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_EQUALTO'),
				),
				'Equal' => array(
					'option_category' => 'string',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_EQUAL'),
				),
				'Float' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_FLOAT'),
				),
				'InArray' => array(
					'option_category' => 'array',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_INARRAY'),
				),
				'Integer' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_INTEGER'),
				),
				'Ip' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_IP'),
				),
				'Mail' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_MAIL'),
				),
				'MaxLength' => array(
					'option_category' => 'integer',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_MAXLENGTH'),
				),
				'Max' => array(
					'option_category' => 'integer',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_MAX'),
				),
				'MinLength' => array(
					'option_category' => 'integer',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_MINLENGTH'),
				),
				'Min' => array(
					'option_category' => 'integer',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_MIN'),
				),
				'NotEmpty' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_NOTEMPTY'),
				),
				'Numeric' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_NUMERIC'),
				),
				'Require' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_REQUIRE'),
				),
				'Url' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_URL'),
				),
				'DateTime' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_DATETIME'),
				),
				'NonNegativeInteger' => array(
					'option_category' => 'boolean',
					'message' => Lang::_('SRV_ENUM_BUILDER_FIELD_VALIDATORS_MESSAGE_NONNEGATIVEINTEGER'),
				)
			);
		}

		return $enum;
	}
}
