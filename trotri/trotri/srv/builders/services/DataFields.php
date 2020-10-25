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
 * DataFields class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataFields.php 1 2014-05-27 18:21:05Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class DataFields
{
	/**
	 * @var string 是否自动递增：y
	 */
	const COLUMN_AUTO_INCREMENT_Y = 'y';

	/**
	 * @var string 是否自动递增：n
	 */
	const COLUMN_AUTO_INCREMENT_N = 'n';

	/**
	 * @var string 是否无符号：y
	 */
	const COLUMN_UNSIGNED_Y = 'y';

	/**
	 * @var string 是否无符号：n
	 */
	const COLUMN_UNSIGNED_N = 'n';

	/**
	 * @var string 表单是否必填：y
	 */
	const FORM_REQUIRED_Y = 'y';

	/**
	 * @var string 表单是否必填：n
	 */
	const FORM_REQUIRED_N = 'n';

	/**
	 * @var string 编辑表单是否中允许输入：y
	 */
	const FORM_MODIFIABLE_Y = 'y';

	/**
	 * @var string 编辑表单是否中允许输入：n
	 */
	const FORM_MODIFIABLE_N = 'n';

	/**
	 * @var string 是否在列表中展示：y
	 */
	const INDEX_SHOW_Y = 'y';

	/**
	 * @var string 是否在列表中展示：n
	 */
	const INDEX_SHOW_N = 'n';

	/**
	 * @var string 是否在新增表单中展示：y
	 */
	const FORM_CREATE_SHOW_Y = 'y';

	/**
	 * @var string 是否在新增表单中展示：n
	 */
	const FORM_CREATE_SHOW_N = 'n';

	/**
	 * @var string 是否在编辑表单中展示：y
	 */
	const FORM_MODIFY_SHOW_Y = 'y';

	/**
	 * @var string 是否在编辑表单中展示：n
	 */
	const FORM_MODIFY_SHOW_N = 'n';

	/**
	 * @var string 是否在查询表单中展示：y
	 */
	const FORM_SEARCH_SHOW_Y = 'y';

	/**
	 * @var string 是否在查询表单中展示：n
	 */
	const FORM_SEARCH_SHOW_N = 'n';

	/**
	 * 获取“是否自动递增”所有选项
	 * @return array
	 */
	public static function getColumnAutoIncrementEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::COLUMN_AUTO_INCREMENT_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::COLUMN_AUTO_INCREMENT_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否无符号”所有选项
	 * @return array
	 */
	public static function getColumnUnsignedEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::COLUMN_UNSIGNED_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::COLUMN_UNSIGNED_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“表单是否必填”所有选项
	 * @return array
	 */
	public static function getFormRequiredEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::FORM_REQUIRED_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::FORM_REQUIRED_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“编辑表单是否不允许输入”所有选项
	 * @return array
	 */
	public static function getFormModifiableEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::FORM_MODIFIABLE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::FORM_MODIFIABLE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否在列表中展示”所有选项
	 * @return array
	 */
	public static function getIndexShowEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::INDEX_SHOW_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::INDEX_SHOW_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否在新增表单中展示”所有选项
	 * @return array
	 */
	public static function getFormCreateShowEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::FORM_CREATE_SHOW_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::FORM_CREATE_SHOW_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否在编辑表单中展示”所有选项
	 * @return array
	 */
	public static function getFormModifyShowEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::FORM_MODIFY_SHOW_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::FORM_MODIFY_SHOW_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否在查询表单中展示”所有选项
	 * @return array
	 */
	public static function getFormSearchShowEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::FORM_SEARCH_SHOW_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::FORM_SEARCH_SHOW_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“表单提示示例”所有选项
	 * @return array
	 */
	public static function getFormPromptExamplesEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				'example0' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE0'),
				'example1' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE1'),
				'example2' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE2'),
				'example3' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE3'),
				'example4' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE4'),
				'example5' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE5'),
				'example6' => Lang::_('SRV_ENUM_BUILDER_FIELDS_FORM_PROMPT_EXAMPLE6'),
			);
		}

		return $enum;
	}
}
