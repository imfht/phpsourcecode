<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use member\library\Lang;

/**
 * DataPortal class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataPortal.php 1 2014-11-26 21:46:18Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class DataPortal
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
	 * @var string 登录方式：partner
	 */
	const LOGIN_TYPE_PARTNER = 'partner';

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
	 * 获取“登录方式”所有选项
	 * @return array
	 */
	public static function getLoginTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::LOGIN_TYPE_MAIL => Lang::_('SRV_ENUM_MEMBER_PORTAL_LOGIN_TYPE_MAIL'),
				self::LOGIN_TYPE_NAME => Lang::_('SRV_ENUM_MEMBER_PORTAL_LOGIN_TYPE_NAME'),
				self::LOGIN_TYPE_PHONE => Lang::_('SRV_ENUM_MEMBER_PORTAL_LOGIN_TYPE_PHONE'),
				self::LOGIN_TYPE_PARTNER => Lang::_('SRV_ENUM_MEMBER_PORTAL_LOGIN_TYPE_PARTNER'),
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

}
