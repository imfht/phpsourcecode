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

use libsrv\Service;

/**
 * DataMembers class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataMembers.php 1 2014-11-27 17:10:30Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class DataMembers
{
	/**
	 * @var string 操作方式：增加“成长值”、“预存款金额”和“积分”
	 */
	const OP_TYPE_INCREASE = 'increase';

	/**
	 * @var string 操作方式：扣除“成长值”、“预存款金额”和“积分”
	 */
	const OP_TYPE_REDUCE = 'reduce';

	/**
	 * @var string 操作方式：冻结“预存款金额”和“积分”
	 */
	const OP_TYPE_FREEZE = 'freeze';

	/**
	 * @var string 操作方式：解冻“预存款金额”和“积分”
	 */
	const OP_TYPE_UNFREEZE = 'unfreeze';

	/**
	 * @var string 操作方式：扣除“预存款冻结金额”和“冻结积分”
	 */
	const OP_TYPE_REDUCE_FREEZE = 'reduce_freeze';

	/**
	 * @var string 来源：管理员操作
	 */
	const SOURCE_ADMINOP = 'adminop';

	/**
	 * @var string 来源：登录
	 */
	const SOURCE_LOGIN = 'login';

	/**
	 * @var string 来源：每日签到
	 */
	const SOURCE_SIGNIN = 'signin';

	/**
	 * @var string 来源：提交订单
	 */
	const SOURCE_P_ORDER = 'p_order';

	/**
	 * @var string 来源：取消订单
	 */
	const SOURCE_C_ORDER = 'c_order';

	/**
	 * 获取“会员成长度”所有选项
	 * @return array
	 */
	public static function getRanksEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = Service::getInstance('Ranks', 'member')->getRankNames();
		}

		return $enum;
	}

	/**
	 * 获取“会员类型”所有选项
	 * @return array
	 */
	public static function getTypesEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = Service::getInstance('Types', 'member')->getTypeNames();
		}

		return $enum;
	}
}
