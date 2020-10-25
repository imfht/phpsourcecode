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
 * DataSocial class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataSocial.php 1 2014-12-01 11:37:11Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class DataSocial
{
	/**
	 * @var string 性别：male
	 */
	const SEX_MALE = 'male';

	/**
	 * @var string 性别：female
	 */
	const SEX_FEMALE = 'female';

	/**
	 * @var string 性别：unknow
	 */
	const SEX_UNKNOW = 'unknow';

	/**
	 * @var string 是否公开生日：y
	 */
	const IS_PUB_BIRTH_Y = 'y';

	/**
	 * @var string 是否公开生日：n
	 */
	const IS_PUB_BIRTH_N = 'n';

	/**
	 * @var string 是否公开纪念日：y
	 */
	const IS_PUB_ANNIVERSARY_Y = 'y';

	/**
	 * @var string 是否公开纪念日：n
	 */
	const IS_PUB_ANNIVERSARY_N = 'n';

	/**
	 * @var string 是否公开兴趣爱好：y
	 */
	const IS_PUB_INTERESTS_Y = 'y';

	/**
	 * @var string 是否公开兴趣爱好：n
	 */
	const IS_PUB_INTERESTS_N = 'n';

	/**
	 * @var string 是否公开手机号：y
	 */
	const IS_PUB_MOBIPHONE_Y = 'y';

	/**
	 * @var string 是否公开手机号：n
	 */
	const IS_PUB_MOBIPHONE_N = 'n';

	/**
	 * @var string 是否公开邮箱：y
	 */
	const IS_PUB_EMAIL_Y = 'y';

	/**
	 * @var string 是否公开邮箱：n
	 */
	const IS_PUB_EMAIL_N = 'n';

	/**
	 * @var string 兴趣爱好
	 */
	const INTERESTS_BOOKS = 'books';
	const INTERESTS_IT = 'it';
	const INTERESTS_CAR = 'car';
	const INTERESTS_COSMETIC = 'cosmetic';
	const INTERESTS_EXERCISE = 'exercise';

	/**
	 * 获取“性别”所有选项
	 * @return array
	 */
	public static function getSexEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SEX_MALE => Lang::_('SRV_ENUM_MEMBER_SOCIAL_SEX_MALE'),
				self::SEX_FEMALE => Lang::_('SRV_ENUM_MEMBER_SOCIAL_SEX_FEMALE'),
				self::SEX_UNKNOW => Lang::_('SRV_ENUM_MEMBER_SOCIAL_SEX_UNKNOW'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否公开生日”所有选项
	 * @return array
	 */
	public static function getIsPubBirthEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUB_BIRTH_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUB_BIRTH_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否公开纪念日”所有选项
	 * @return array
	 */
	public static function getIsPubAnniversaryEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUB_ANNIVERSARY_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUB_ANNIVERSARY_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否公开兴趣爱好”所有选项
	 * @return array
	 */
	public static function getIsPubInterestsEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUB_INTERESTS_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUB_INTERESTS_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否公开手机号”所有选项
	 * @return array
	 */
	public static function getIsPubMobiphoneEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUB_MOBIPHONE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUB_MOBIPHONE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否公开邮箱”所有选项
	 * @return array
	 */
	public static function getIsPubEmailEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUB_EMAIL_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUB_EMAIL_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“兴趣爱好”所有选项
	 * @return array
	 */
	public static function getInterestsEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::INTERESTS_BOOKS => Lang::_('SRV_ENUM_MEMBER_SOCIAL_INTERESTS_BOOKS'),
				self::INTERESTS_IT => Lang::_('SRV_ENUM_MEMBER_SOCIAL_INTERESTS_IT'),
				self::INTERESTS_CAR => Lang::_('SRV_ENUM_MEMBER_SOCIAL_INTERESTS_CAR'),
				self::INTERESTS_COSMETIC => Lang::_('SRV_ENUM_MEMBER_SOCIAL_INTERESTS_COSMETIC'),
				self::INTERESTS_EXERCISE => Lang::_('SRV_ENUM_MEMBER_SOCIAL_INTERESTS_EXERCISE'),
			);
		}

		return $enum;
	}
}
