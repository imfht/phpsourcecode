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
 * DataRepwd class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataRepwd.php 1 2014-08-28 10:09:58Z huan.song $
 * @package member.services
 * @since 1.0
 */
class DataRepwd
{
	/**
	 * @var integer 修改密码成功
	 */
	const SUCCESS_REPWD_NUM              = 0;

	/**
	 * @var integer 修改密码失败
	 */
	const ERROR_REPWD_FAILED             = 1001;

	/**
	 * @var integer 原始密码为空
	 */
	const ERROR_OLD_PASSWORD_EMPTY    = 3001;

	/**
	 * @var integer 原始密码错误
	 */
	const ERROR_OLD_PASSWORD_WRONG       = 3002;

	/**
	 * @var integer 会员邮箱为空
	 */
	const ERROR_MEMBER_MAIL_EMPTY     = 4001;

	/**
	 * @var integer 会员邮箱错误
	 */
	const ERROR_MEMBER_MAIL_WRONG        = 4002;

	/**
	 * @var integer 会员邮箱不存在
	 */
	const ERROR_MEMBER_MAIL_NOT_EXISTS   = 4003;

	/**
	 * @var integer 发送邮件失败
	 */
	const ERROR_SEND_MAIL_FAILED         = 4004;

	/**
	 * @var integer 密文为空
	 */
	const ERROR_CIPHERTEXT_EMPTY         = 4005;

	/**
	 * @var integer 解密失败
	 */
	const ERROR_CIPHERTEXT_DECRYPT_FAILED = 4006;

	/**
	 * @var integer 密文已过期
	 */
	const ERROR_CIPHERTEXT_TIME_OUT      = 4007;

	/**
	 * @var integer 密文错误
	 */
	const ERROR_CIPHERTEXT_WRONG         = 4008;

	/**
	 * 获取“错误信息”所有选项
	 * @return array
	 */
	public static function getErrMsgEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SUCCESS_REPWD_NUM               => Lang::_('SRV_FILTER_REPWD_REPWD_SUCCESS'),
				self::ERROR_REPWD_FAILED              => Lang::_('SRV_FILTER_REPWD_REPWD_FAILED'),
				self::ERROR_OLD_PASSWORD_EMPTY        => Lang::_('SRV_FILTER_REPWD_OLD_PASSWORD_NOTEMPTY'),
				self::ERROR_OLD_PASSWORD_WRONG        => Lang::_('SRV_FILTER_REPWD_OLD_PASSWORD_WRONG'),
				self::ERROR_MEMBER_MAIL_EMPTY         => Lang::_('SRV_FILTER_REPWD_MEMBER_MAIL_NOTEMPTY'),
				self::ERROR_MEMBER_MAIL_WRONG         => Lang::_('SRV_FILTER_REPWD_MEMBER_MAIL_WRONG'),
				self::ERROR_MEMBER_MAIL_NOT_EXISTS    => Lang::_('SRV_FILTER_REPWD_MEMBER_MAIL_NOT_EXISTS'),
				self::ERROR_SEND_MAIL_FAILED          => Lang::_('SRV_FILTER_REPWD_SEND_MAIL_FAILED'),
				self::ERROR_CIPHERTEXT_EMPTY          => Lang::_('SRV_FILTER_REPWD_CIPHERTEXT_NOTEMPTY'),
				self::ERROR_CIPHERTEXT_DECRYPT_FAILED => Lang::_('SRV_FILTER_REPWD_CIPHERTEXT_WRONG'),
				self::ERROR_CIPHERTEXT_TIME_OUT       => Lang::_('SRV_FILTER_REPWD_CIPHERTEXT_TIME_OUT'),
				self::ERROR_CIPHERTEXT_WRONG          => Lang::_('SRV_FILTER_REPWD_CIPHERTEXT_WRONG')
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
