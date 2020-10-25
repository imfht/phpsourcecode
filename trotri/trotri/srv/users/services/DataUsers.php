<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\services;

use users\library\Lang;

/**
 * DataUsers class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataUsers.php 1 2014-08-07 10:09:58Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class DataUsers
{
	/**
	 * @var string 登录方式：mail
	 */
	const LOGIN_TYPE_MAIL = 'mail';

	/**
	 * @var string 登录方式：name
	 */
	const LOGIN_TYPE_NAME = 'name';

	/**
	 * @var string 登录方式：phone
	 */
	const LOGIN_TYPE_PHONE = 'phone';

	/**
	 * @var string 是否已验证邮箱：y
	 */
	const VALID_MAIL_Y = 'y';

	/**
	 * @var string 是否已验证邮箱：n
	 */
	const VALID_MAIL_N = 'n';

	/**
	 * @var string 是否已验证手机号：y
	 */
	const VALID_PHONE_Y = 'y';

	/**
	 * @var string 是否已验证手机号：n
	 */
	const VALID_PHONE_N = 'n';

	/**
	 * @var string 是否禁用：y
	 */
	const FORBIDDEN_Y = 'y';

	/**
	 * @var string 是否禁用：n
	 */
	const FORBIDDEN_N = 'n';

	/**
	 * @var string 是否删除：y
	 */
	const TRASH_Y = 'y';

	/**
	 * @var string 是否删除：n
	 */
	const TRASH_N = 'n';

	/**
	 * @var string 性别：男
	 */
	const SEX_MALE = 'm';

	/**
	 * @var string 性别：女
	 */
	const SEX_FEMALE = 'f';

	/**
	 * @var string 性别：保密
	 */
	const SEX_UNKNOW = 'u';

	/**
	 * 获取“登录方式”所有选项
	 * @return array
	 */
	public static function getLoginTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::LOGIN_TYPE_MAIL => Lang::_('SRV_ENUM_USERS_LOGIN_TYPE_MAIL'),
				self::LOGIN_TYPE_NAME => Lang::_('SRV_ENUM_USERS_LOGIN_TYPE_NAME'),
				self::LOGIN_TYPE_PHONE => Lang::_('SRV_ENUM_USERS_LOGIN_TYPE_PHONE'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否已验证邮箱”所有选项
	 * @return array
	 */
	public static function getValidMailEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::VALID_MAIL_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::VALID_MAIL_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否已验证手机号”所有选项
	 * @return array
	 */
	public static function getValidPhoneEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::VALID_PHONE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::VALID_PHONE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

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

	/**
	 * 获取“是否删除”所有选项
	 * @return array
	 */
	public static function getTrashEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::TRASH_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::TRASH_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“用户分组ID”所有选项
	 * @return array
	 */
	public static function getGroupIdsEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$groups = new Groups();
			$enum = $groups->getGroupIds();
		}

		return $enum;
	}

	/**
	 * 获取“性别”所有选项
	 * @return array
	 */
	public static function getSexEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SEX_MALE => Lang::_('SRV_ENUM_USERS_SEX_MALE'),
				self::SEX_FEMALE => Lang::_('SRV_ENUM_USERS_SEX_FEMALE'),
				self::SEX_UNKNOW => Lang::_('SRV_ENUM_USERS_SEX_UNKNOW'),
			);
		}

		return $enum;
	}
}
