<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\library;

/**
 * TableNames class file
 * 表名管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableNames.php 1 2013-04-05 01:08:06Z huan.song $
 * @package posts.library
 * @since 1.0
 */
class TableNames
{
	/**
	 * 获取“文档模型表”表名
	 * @return string
	 */
	public static function getModules()
	{
		return 'post_modules';
	}

	/**
	 * 获取“文档类别表”表名
	 * @return string
	 */
	public static function getCategories()
	{
		return 'post_categories';
	}

	/**
	 * 获取“文档管理表”表名
	 * @return string
	 */
	public static function getPosts()
	{
		return 'posts';
	}

	/**
	 * 获取“文档扩展表”表名
	 * @return string
	 */
	public static function getPostProfile()
	{
		return 'post_profile';
	}

	/**
	 * 获取“文档评论表”表名
	 * @return string
	 */
	public static function getComments()
	{
		return 'post_comments';
	}
}
