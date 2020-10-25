<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\adverts\helpers;

use libsrv\Service;

/**
 * Adverts class file
 * 广告帮助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Adverts.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.adverts.helpers
 * @since 1.0
 */
class Adverts
{
	/**
	 * 查询指定位置下一条广告
	 * @param string $typeKey
	 * @return array
	 */
	public static function getRow($typeKey)
	{
		$row = self::getService()->getRow($typeKey);
		return $row;
	}

	/**
	 * 查询指定位置下多条广告
	 * @param string $typeKey
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public static function findRows($typeKey, $limit = 0, $offset = 0)
	{
		$rows = self::getService()->findRows($typeKey, $limit, $offset);
		return $rows;
	}

	/**
	 * 获取广告业务处理类
	 * @return \advert\services\Adverts
	 */
	public static function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Adverts', 'advert');
		}

		return $service;
	}

}
