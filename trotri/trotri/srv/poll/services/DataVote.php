<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace poll\services;

use poll\library\Lang;

/**
 * DataVote class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataVote.php 1 2014-12-05 17:47:10Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class DataVote
{
	/**
	 * @var integer 投票成功
	 */
	const SUCCESS_NUM                    = 0;

	/**
	 * @var integer 投票失败
	 */
	const ERROR_FAILED                   = 1001;

	/**
	 * @var integer 未开始
	 */
	const ERROR_DT_PUBLISH_UP_WRONG      = 2001;

	/**
	 * @var integer 已结束
	 */
	const ERROR_DT_PUBLISH_DOWN_WRONG    = 2002;

	/**
	 * @var integer 禁止非会员
	 */
	const ERROR_ALLOW_UNREGISTERED_WRONG = 3001;

	/**
	 * @var integer 禁止的会员成长度
	 */
	const ERROR_M_RANK_ID_WRONG          = 3002;

	/**
	 * @var integer 选项为空
	 */
	const ERROR_POLLOPTIONS_EMPTY        = 4001;

	/**
	 * @var integer 超过最多可选数量
	 */
	const ERROR_POLLOPTIONS_WRONG        = 4002;

	/**
	 * @var integer 选项和投票对不上
	 */
	const ERROR_POLLOPTIONS_NOT_EXISTS   = 4003;

	/**
	 * @var integer 已经参与过了
	 */
	const ERROR_JOIN_TYPE_FOREVER_WRONG  = 5001;

	/**
	 * @var integer 今年已经参与过了
	 */
	const ERROR_JOIN_TYPE_YEAR_WRONG     = 5002;

	/**
	 * @var integer 本月已经参与过了
	 */
	const ERROR_JOIN_TYPE_MONTH_WRONG    = 5003;

	/**
	 * @var integer 今天已经参与过了
	 */
	const ERROR_JOIN_TYPE_DAY_WRONG      = 5004;

	/**
	 * @var integer 当前小时已经参与过了
	 */
	const ERROR_JOIN_TYPE_HOUR_WRONG     = 5005;

	/**
	 * @var integer n秒后才能再次参与
	 */
	const ERROR_JOIN_TYPE_INTERVAL_WRONG = 5006;

	/**
	 * 获取“错误信息”所有选项
	 * @return array
	 */
	public static function getErrMsgEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SUCCESS_NUM                    => Lang::_('SRV_FILTER_VOTE_SUCCESS'),
				self::ERROR_FAILED                   => Lang::_('SRV_FILTER_VOTE_FAILED'),
				self::ERROR_DT_PUBLISH_UP_WRONG      => Lang::_('SRV_FILTER_VOTE_DT_PUBLISH_UP_WRONG'),
				self::ERROR_DT_PUBLISH_DOWN_WRONG    => Lang::_('SRV_FILTER_VOTE_DT_PUBLISH_DOWN_WRONG'),
				self::ERROR_ALLOW_UNREGISTERED_WRONG => Lang::_('SRV_FILTER_VOTE_ALLOW_UNREGISTERED_WRONG'),
				self::ERROR_M_RANK_ID_WRONG          => Lang::_('SRV_FILTER_VOTE_M_RANK_ID_WRONG'),
				self::ERROR_POLLOPTIONS_EMPTY        => Lang::_('SRV_FILTER_VOTE_POLLOPTIONS_EMPTY'),
				self::ERROR_POLLOPTIONS_WRONG        => Lang::_('SRV_FILTER_VOTE_POLLOPTIONS_WRONG'),
				self::ERROR_POLLOPTIONS_NOT_EXISTS   => Lang::_('SRV_FILTER_VOTE_POLLOPTIONS_NOT_EXISTS'),
				self::ERROR_JOIN_TYPE_FOREVER_WRONG  => Lang::_('SRV_FILTER_VOTE_JOIN_TYPE_FOREVER_WRONG'),
				self::ERROR_JOIN_TYPE_YEAR_WRONG     => Lang::_('SRV_FILTER_VOTE_JOIN_TYPE_YEAR_WRONG'),
				self::ERROR_JOIN_TYPE_MONTH_WRONG    => Lang::_('SRV_FILTER_VOTE_JOIN_TYPE_MONTH_WRONG'),
				self::ERROR_JOIN_TYPE_DAY_WRONG      => Lang::_('SRV_FILTER_VOTE_JOIN_TYPE_DAY_WRONG'),
				self::ERROR_JOIN_TYPE_HOUR_WRONG     => Lang::_('SRV_FILTER_VOTE_JOIN_TYPE_HOUR_WRONG'),
				self::ERROR_JOIN_TYPE_INTERVAL_WRONG => Lang::_('SRV_FILTER_VOTE_JOIN_TYPE_INTERVAL_WRONG'),
			);
		}

		return $enum;
	}

	/**
	 * 通过“错误码”获取“错误信息”
	 * @param integer $errNo
	 * @param mixed $args
	 * @return string
	 */
	public static function getErrMsgByErrNo($errNo, $args = '')
	{
		$errNo = (int) $errNo;
		$enum = self::getErrMsgEnum();

		$errMsg = isset($enum[$errNo]) ? $enum[$errNo] : Lang::_('SRV_FILTER_VOTE_FAILED');
		if ($errNo === self::ERROR_DT_PUBLISH_UP_WRONG || $errNo === self::ERROR_POLLOPTIONS_WRONG) {
			$errMsg = sprintf($errMsg, $args);
			return $errMsg;
		}

		if ($errNo === self::ERROR_JOIN_TYPE_INTERVAL_WRONG) {
			$second = (int) $args;

			if ($second < 60) {
				$errMsg = sprintf($errMsg, $second, Lang::_('SRV_ENUM_VOTE_INTERVAL_SECOND'), $second, Lang::_('SRV_ENUM_VOTE_INTERVAL_SECOND'));
				return $errMsg;
			}

			$minute = floor($second / 60);
			if ($second > ($minute * 60)) {
				$errMsg = sprintf($errMsg, $second, Lang::_('SRV_ENUM_VOTE_INTERVAL_SECOND'), $second, Lang::_('SRV_ENUM_VOTE_INTERVAL_SECOND'));
				return $errMsg;
			}

			if ($minute < 60) {
				$errMsg = sprintf($errMsg, $minute, Lang::_('SRV_ENUM_VOTE_INTERVAL_MINUTE'), $minute, Lang::_('SRV_ENUM_VOTE_INTERVAL_MINUTE'));
				return $errMsg;
			}

			$hour = floor($minute / 60);
			if ($minute > ($hour * 60)) {
				$errMsg = sprintf($errMsg, $minute, Lang::_('SRV_ENUM_VOTE_INTERVAL_MINUTE'), $minute, Lang::_('SRV_ENUM_VOTE_INTERVAL_MINUTE'));
				return $errMsg;
			}

			if ($hour < 24) {
				$errMsg = sprintf($errMsg, $hour, Lang::_('SRV_ENUM_VOTE_INTERVAL_HOUR'), $hour, Lang::_('SRV_ENUM_VOTE_INTERVAL_HOUR'));
				return $errMsg;
			}

			$day = floor($hour / 24);
			if ($hour > ($day * 24)) {
				$errMsg = sprintf($errMsg, $hour, Lang::_('SRV_ENUM_VOTE_INTERVAL_HOUR'), $hour, Lang::_('SRV_ENUM_VOTE_INTERVAL_HOUR'));
				return $errMsg;
			}

			$errMsg = sprintf($errMsg, $day, Lang::_('SRV_ENUM_VOTE_INTERVAL_DAY'), $day, Lang::_('SRV_ENUM_VOTE_INTERVAL_DAY'));
			return $errMsg;
		}

		return $errMsg;
	}
}
