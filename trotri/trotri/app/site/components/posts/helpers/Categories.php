<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\posts\helpers;

use libsrv\Service;

/**
 * Categories class file
 * 文档类别帮助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Categories.php 1 2014-10-13 21:17:13Z Code Generator $
 * @package components.posts.helpers
 * @since 1.0
 */
class Categories
{
	/**
	 * 通过“类别ID”，获取“类别名”
	 * @param integer $catId
	 * @return string
	 */
	public static function getCatNameByCatId($catId)
	{
		$catName = self::getService()->getCategoryNameByCategoryId($catId);
		return $catName;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $catId
	 * @return array
	 */
	public static function findByPk($catId)
	{
		$row = self::getService()->findByPk($catId);
		return $row;
	}

	/**
	 * 获取文档类别业务处理类
	 * @return \posts\services\Categories
	 */
	public static function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Categories', 'posts');
		}

		return $service;
	}
}
