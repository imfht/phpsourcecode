<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace system\library;

/**
 * TableNames class file
 * 表名管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableNames.php 1 2013-04-05 01:08:06Z huan.song $
 * @package system.library
 * @since 1.0
 */
class TableNames
{
	/**
	 * 获取“站点配置表”表名
	 * @return string
	 */
	public static function getOptions()
	{
		return 'system_options';
	}

	/**
	 * 获取“地区表”表名
	 * @return string
	 */
	public static function getRegions()
	{
		return 'regions';
	}
}
