<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace poll\library;

/**
 * TableNames class file
 * 表名管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableNames.php 1 2013-04-05 01:08:06Z huan.song $
 * @package poll.library
 * @since 1.0
 */
class TableNames
{
	/**
	 * 获取“投票表”表名
	 * @return string
	 */
	public static function getPolls()
	{
		return 'polls';
	}

	/**
	 * 获取“投票选项表”表名
	 * @return string
	 */
	public static function getPolloptions()
	{
		return 'polloptions';
	}

	/**
	 * 获取“会员投票日志表”表名
	 * @return string
	 */
	public static function getPollMemberLogs()
	{
		return 'poll_member_logs';
	}

	/**
	 * 获取“游客投票日志表”表名
	 * @return string
	 */
	public static function getPollVisitorLogs()
	{
		return 'poll_visitor_logs';
	}

}
