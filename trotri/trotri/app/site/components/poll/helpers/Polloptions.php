<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\poll\helpers;

use libsrv\Service;

/**
 * Polloptions class file
 * 投票选项帮助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polloptions.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.poll.helpers
 * @since 1.0
 */
class Polloptions
{
	/**
	 * 通过“投票ID”，获取所有的选项
	 * @param string $pollKey
	 * @return array
	 */
	public static function findRows($pollId)
	{
		$rows = self::getService()->findAllByPollId($pollId);
		return $rows;
	}

	/**
	 * 获取投票选项业务处理类
	 * @return \poll\services\Polloptions
	 */
	public static function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Polloptions', 'poll');
		}

		return $service;
	}

}
