<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\services;

use libsrv\AbstractService;
use tfc\saf\Log;

/**
 * Usergroups class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Usergroups.php 1 2014-08-06 15:36:21Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class Usergroups extends AbstractService
{
	/**
	 * 通过用户ID，获取该用户所属的组ID
	 * @param integer $userId
	 * @return array
	 */
	public function findGroupIdsByUserId($userId)
	{
		$data = array();

		$rows = $this->getDb()->findGroupIdsByUserId($userId);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$data[] = (int) $row['group_id'];
			}
		}

		return $data;
	}

	/**
	 * 刷新用户的所有分组
	 * @param integer $userId
	 * @param array $groupIds
	 * @return array
	 */
	public function modify($userId, array $groupIds)
	{
		if (($userId = (int) $userId) <= 0) {
			Log::warning(sprintf(
				'Usergroups user_id "%d" must be greater than 0', $userId
			), 0,  __METHOD__);

			return false;
		}

		$news = array();
		foreach ($groupIds as $value) {
			if (($value = (int) $value) > 0) {
				if (!in_array($value, $news)) {
					$news[] = $value;
				}
			}
		}

		$olds = $this->findGroupIdsByUserId($userId);

		$groupIdCreates = array_diff($news, $olds);
		$groupIdRemoves = array_diff($olds, $news);

		$rowCountCreate = $this->getDb()->batchCreate($userId, $groupIdCreates);
		$rowCountRemove = $this->getDb()->batchRemove($userId, $groupIdRemoves);

		$totalCreate = count($groupIdCreates);
		$totalRemove = count($groupIdRemoves);

		$errorCreate = $totalCreate - $rowCountCreate;
		$errorRemove = $totalRemove - $rowCountRemove;

		if ($errorCreate > 0 || $errorRemove > 0) {
			Log::warning(sprintf(
				'Usergroups user_id "%d", group_ids "%s", Create {total "%d", success "%d", error "%d"}, Remove {total "%d", success "%d", error "%d"}', 
				$userId, serialize($groupIds), $totalCreate, $rowCountCreate, $errorCreate, $totalRemove, $rowCountRemove, $errorRemove
			), 0,  __METHOD__);

			return false;
		}

		return true;
	}
}
