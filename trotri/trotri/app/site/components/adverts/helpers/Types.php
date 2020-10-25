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
 * Types class file
 * 广告位置帮助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Types.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.adverts.helpers
 * @since 1.0
 */
class Types
{
	/**
	 * 通过“位置Key”，获取“位置名”
	 * @param string $typeKey
	 * @return string
	 */
	public static function getTypeNameByTypeKey($typeKey)
	{
		$typeName = self::getService()->getTypeNameByTypeKey($typeKey);
		return $typeName;
	}

	/**
	 * 获取广告业务处理类
	 * @return \advert\services\Adverts
	 */
	public static function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Types', 'advert');
		}

		return $service;
	}

}
