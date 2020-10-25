<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\library;

/**
 * TableNames class file
 * 表名管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableNames.php 1 2013-04-05 01:08:06Z huan.song $
 * @package member.library
 * @since 1.0
 */
class TableNames
{
	/**
	 * 获取“会员类型表”表名
	 * @return string
	 */
	public static function getTypes()
	{
		return 'member_types';
	}

	/**
	 * 获取“会员成长度表”表名
	 * @return string
	 */
	public static function getRanks()
	{
		return 'member_ranks';
	}

	/**
	 * 获取“会员登录表”表名
	 * @return string
	 */
	public static function getPortal()
	{
		return 'member_portal';
	}

	/**
	 * 获取“会员主表”表名
	 * @return string
	 */
	public static function getMembers()
	{
		return 'members';
	}

	/**
	 * 获取“会员详情表”表名
	 * @return string
	 */
	public static function getSocial()
	{
		return 'member_social';
	}

	/**
	 * 获取“会员地址表”表名
	 * @return string
	 */
	public static function getAddresses()
	{
		return 'member_addresses';
	}

	/**
	 * 获取“会员扩展表”表名
	 * @return string
	 */
	public static function getProfile()
	{
		return 'member_profile';
	}

	/**
	 * 获取“会员预存款日志表”表名
	 * @return string
	 */
	public static function getBalanceLogs()
	{
		return 'member_balance_logs';
	}

	/**
	 * 获取“会员积分日志表”表名
	 * @return string
	 */
	public static function getPointsLogs()
	{
		return 'member_points_logs';
	}

	/**
	 * 获取“会员成长值日志表”表名
	 * @return string
	 */
	public static function getExperienceLogs()
	{
		return 'member_experience_logs';
	}
}
