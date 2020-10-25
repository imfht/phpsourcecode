<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace system\services;

use system\library\Lang;

/**
 * DataOptions class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataOptions.php 1 2014-08-19 00:15:56Z Code Generator $
 * @package system.services
 * @since 1.0
 */
class DataOptions
{
	/**
	 * @var string 使用重写模式获取URLS：y
	 */
	const URL_REWRITE_Y = 'y';

	/**
	 * @var string 使用重写模式获取URLS：n
	 */
	const URL_REWRITE_N = 'n';

	/**
	 * @var string 是否关闭新用户注册：y
	 */
	const CLOSE_REGISTER_Y = 'y';

	/**
	 * @var string 是否关闭新用户注册：n
	 */
	const CLOSE_REGISTER_N = 'n';

	/**
	 * @var string 是否显示用户注册协议：y
	 */
	const SHOW_REGISTER_SERVICE_ITEM_Y = 'y';

	/**
	 * @var string 是否显示用户注册协议：n
	 */
	const SHOW_REGISTER_SERVICE_ITEM_N = 'n';

	/**
	 * @var string 水印类型：imgdir
	 */
	const WATER_MARK_TYPE_IMGDIR = 'imgdir';

	/**
	 * @var string 水印类型：text
	 */
	const WATER_MARK_TYPE_TEXT = 'text';

	/**
	 * @var string 水印类型：none
	 */
	const WATER_MARK_TYPE_NONE = 'none';

	/**
	 * @var string 水印放置位置：1
	 */
	const WATER_MARK_POSITION_1 = 1;

	/**
	 * @var string 水印放置位置：2
	 */
	const WATER_MARK_POSITION_2 = 2;

	/**
	 * @var string 水印放置位置：3
	 */
	const WATER_MARK_POSITION_3 = 3;

	/**
	 * @var string 水印放置位置：4
	 */
	const WATER_MARK_POSITION_4 = 4;

	/**
	 * @var string 水印放置位置：5
	 */
	const WATER_MARK_POSITION_5 = 5;

	/**
	 * @var string 水印放置位置：6
	 */
	const WATER_MARK_POSITION_6 = 6;

	/**
	 * @var string 水印放置位置：7
	 */
	const WATER_MARK_POSITION_7 = 7;

	/**
	 * @var string 水印放置位置：8
	 */
	const WATER_MARK_POSITION_8 = 8;

	/**
	 * @var string 水印放置位置：9
	 */
	const WATER_MARK_POSITION_9 = 9;

	/**
	 * 获取“使用重写模式获取URLS”所有选项
	 * @return array
	 */
	public static function getUrlRewriteEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::URL_REWRITE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::URL_REWRITE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否关闭新用户注册”所有选项
	 * @return array
	 */
	public static function getCloseRegisterEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::CLOSE_REGISTER_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::CLOSE_REGISTER_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否显示用户注册协议”所有选项
	 * @return array
	 */
	public static function getShowRegisterServiceItemEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SHOW_REGISTER_SERVICE_ITEM_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::SHOW_REGISTER_SERVICE_ITEM_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“水印类型”所有选项
	 * @return array
	 */
	public static function getWaterMarkTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::WATER_MARK_TYPE_IMGDIR => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_TYPE_IMGDIR'),
				self::WATER_MARK_TYPE_TEXT => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_TYPE_TEXT'),
				self::WATER_MARK_TYPE_NONE => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_TYPE_NONE'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“水印放置位置”所有选项
	 * @return array
	 */
	public static function getWaterMarkPositionEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::WATER_MARK_POSITION_1 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_1'),
				self::WATER_MARK_POSITION_2 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_2'),
				self::WATER_MARK_POSITION_3 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_3'),
				self::WATER_MARK_POSITION_4 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_4'),
				self::WATER_MARK_POSITION_5 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_5'),
				self::WATER_MARK_POSITION_6 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_6'),
				self::WATER_MARK_POSITION_7 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_7'),
				self::WATER_MARK_POSITION_8 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_8'),
				self::WATER_MARK_POSITION_9 => Lang::_('SRV_ENUM_SYSTEM_OPTIONS_WATER_MARK_POSITION_9'),
			);
		}

		return $enum;
	}

}
