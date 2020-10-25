<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\library;

/**
 * TableNames class file
 * 表名管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableNames.php 1 2013-04-05 01:08:06Z huan.song $
 * @package users.library
 * @since 1.0
 */
class TableNames
{
	/**
	 * 获取“用户可访问的事件表”表名
	 * @return string
	 */
	public static function getAmcas()
	{
		return 'user_amcas';
	}

	/**
	 * 获取“用户分组表”表名
	 * @return string
	 */
	public static function getGroups()
	{
		return 'user_groups';
	}

	/**
	 * 获取“用户分组表”表名
	 * @return string
	 */
	public static function getUsergroups()
	{
		return 'user_usergroups_map';
	}

	/**
	 * 获取“用户主表”表名
	 * @return string
	 */
	public static function getUsers()
	{
		return 'users';
	}

	/**
	 * 获取“用户扩展表”表名
	 * @return string
	 */
	public static function getUserProfile()
	{
		return 'user_profile';
	}
}
