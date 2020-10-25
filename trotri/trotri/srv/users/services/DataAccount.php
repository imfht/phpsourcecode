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
 * DataAccount class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataAccount.php 1 2014-08-28 10:09:58Z huan.song $
 * @package users.services
 * @since 1.0
 */
class DataAccount
{
	/**
	 * @var integer 登录成功
	 */
	const SUCCESS_LOGIN_NUM           = 0;

	/**
	 * @var integer 登录失败
	 */
	const ERROR_LOGIN_FAILED          = 3000;

	/**
	 * @var integer 注销账户失败
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
	const ERROR_USER_TRASH            = 3012;

	/**
	 * @var integer 用户已被禁用
	 */
	const ERROR_USER_FORBIDDEN        = 3013;

	/**
	 * @var integer 密码为空
	 */
	const ERROR_PASSWORD_EMPTY        = 3014;

	/**
	 * @var integer 密码错误
	 */
	const ERROR_PASSWORD_WRONG        = 3015;

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
				self::ERROR_USER_TRASH            => Lang::_('SRV_FILTER_ACCOUNT_USER_TRASH'),
				self::ERROR_USER_FORBIDDEN        => Lang::_('SRV_FILTER_ACCOUNT_USER_FORBIDDEN'),
				self::ERROR_PASSWORD_EMPTY        => Lang::_('SRV_FILTER_ACCOUNT_PASSWORD_EMPTY'),
				self::ERROR_PASSWORD_WRONG        => Lang::_('SRV_FILTER_ACCOUNT_PASSWORD_WRONG')
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
