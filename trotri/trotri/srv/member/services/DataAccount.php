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
 * DataAccount class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataAccount.php 1 2014-08-28 10:09:58Z huan.song $
 * @package member.services
 * @since 1.0
 */
class DataAccount
{
	/**
	 * @var string 第三方账号登录：QQ
	 */
	const PARTNER_QQ = 'qq';

	/**
	 * @var string 第三方账号登录：微信
	 */
	const PARTNER_WECHAT = 'wechat';

	/**
	 * @var string 所有的第三方账号登录类型
	 */
	public static $partners = array(
		self::PARTNER_QQ,
		self::PARTNER_WECHAT
	);

	/**
	 * @var integer 登录成功
	 */
	const SUCCESS_LOGIN_NUM           = 0;

	/**
	 * @var integer 登录失败
	 */
	const ERROR_LOGIN_FAILED          = 3000;

	/**
	 * @var integer 退出登录失败
	 */
	const ERROR_LOGOUT_FAILED         = 3001;

	/**
	 * @var integer 更新用户信息失败
	 */
	const ERROR_MODIFY_LAST_LOGIN     = 3002;

	/**
	 * @var integer 登录名为空
	 */
	const ERROR_LOGIN_NAME_EMPTY      = 3010;

	/**
	 * @var integer 登录名不存在
	 */
	const ERROR_LOGIN_NAME_NOT_EXISTS = 3011;

	/**
	 * @var integer 用户已被删除
	 */
	const ERROR_MEMBER_TRASH          = 3012;

	/**
	 * @var integer 用户已被禁用
	 */
	const ERROR_MEMBER_FORBIDDEN      = 3013;

	/**
	 * @var integer 密码为空
	 */
	const ERROR_PASSWORD_EMPTY        = 3014;

	/**
	 * @var integer 密码错误
	 */
	const ERROR_PASSWORD_WRONG        = 3015;

	/**
	 * @var integer 第三方账号登录：类型为空
	 */
	const ERROR_PARTNER_EMPTY         = 4001;

	/**
	 * @var integer 第三方账号登录：类型错误
	 */
	const ERROR_PARTNER_WRONG         = 4002;

	/**
	 * @var integer 第三方账号登录：OpenID为空
	 */
	const ERROR_OPENID_EMPTY          = 4003;

	/**
	 * 获取“错误信息”所有选项
	 * @return array
	 */
	public static function getErrMsgEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SUCCESS_LOGIN_NUM           => Lang::_('SRV_FILTER_ACCOUNT_LOGIN_SUCCESS'),
				self::ERROR_LOGIN_FAILED          => Lang::_('SRV_FILTER_ACCOUNT_LOGIN_FAILED'),
				self::ERROR_MODIFY_LAST_LOGIN     => Lang::_('SRV_FILTER_ACCOUNT_MODIFY_LAST_LOGIN'),
				self::ERROR_LOGIN_NAME_EMPTY      => Lang::_('SRV_FILTER_ACCOUNT_LOGIN_NAME_EMPTY'),
				self::ERROR_LOGIN_NAME_NOT_EXISTS => Lang::_('SRV_FILTER_ACCOUNT_LOGIN_NAME_NOT_EXISTS'),
				self::ERROR_MEMBER_TRASH          => Lang::_('SRV_FILTER_ACCOUNT_MEMBER_TRASH'),
				self::ERROR_MEMBER_FORBIDDEN      => Lang::_('SRV_FILTER_ACCOUNT_MEMBER_FORBIDDEN'),
				self::ERROR_PASSWORD_EMPTY        => Lang::_('SRV_FILTER_ACCOUNT_PASSWORD_EMPTY'),
				self::ERROR_PASSWORD_WRONG        => Lang::_('SRV_FILTER_ACCOUNT_PASSWORD_WRONG'),
				self::ERROR_PARTNER_EMPTY         => Lang::_('SRV_FILTER_ACCOUNT_PARTNER_EMPTY'),
				self::ERROR_PARTNER_WRONG         => Lang::_('SRV_FILTER_ACCOUNT_PARTNER_WRONG'),
				self::ERROR_OPENID_EMPTY          => Lang::_('SRV_FILTER_ACCOUNT_OPENID_EMPTY'),
			);
		}

		return $enum;
	}

	/**
	 * 通过“错误码”获取“错误信息”
	 * @param integer $errNo
	 * @return string
	 */
	public static function getErrMsgByErrNo($errNo)
	{
		$errNo = (int) $errNo;
		$enum = self::getErrMsgEnum();

		return isset($enum[$errNo]) ? $enum[$errNo] : Lang::_('SRV_FILTER_ACCOUNT_UNKNOWN_WRONG');
	}

}
