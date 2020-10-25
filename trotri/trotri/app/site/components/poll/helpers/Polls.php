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
 * Polls class file
 * 投票帮助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polls.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.poll.helpers
 * @since 1.0
 */
class Polls
{
	/**
	 * 通过投票Key查询一条有效的投票
	 * @param string $pollKey
	 * @param boolean $hasOptions
	 * @return array
	 */
	public static function getUsable($pollKey, $hasOptions = true)
	{
		$row = self::getService()->findByPollKey($pollKey, true);
		if ($hasOptions) {
			if ($row && is_array($row) && isset($row['poll_id'])) {
				$row['options'] = Polloptions::findRows($row['poll_id']);
			}
		}

		return $row;
	}

	/**
	 * 通过投票Key查询一条投票
	 * @param string $pollKey
	 * @param boolean $hasOptions
	 * @return array
	 */
	public static function getRow($pollKey, $hasOptions = true)
	{
		$row = self::getService()->findByPollKey($pollKey, false);
		if ($hasOptions) {
			if ($row && is_array($row) && isset($row['poll_id'])) {
				$row['options'] = Polloptions::findRows($row['poll_id']);
			}
		}

		return $row;
	}

	/**
	 * 获取投票业务处理类
	 * @return \poll\services\Polls
	 */
	public static function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Polls', 'poll');
		}

		return $service;
	}

}
