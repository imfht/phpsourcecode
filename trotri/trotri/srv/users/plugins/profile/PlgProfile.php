<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\plugins\profile;

use tfc\ap\Event;
use tdo\Profile;
use libsrv\Service;
use users\library\TableNames;
use users\services\DataUsers;

/**
 * PlgProfile class file
 * 管理用户扩展信息
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PlgProfile.php 1 2013-04-05 01:38:06Z huan.song $
 * @package users.plugins.profile
 * @since 1.0
 */
class PlgProfile extends Event
{
	/**
	 * 查询后执行
	 * @param string $context
	 * @param array $row
	 * @param mixed $params
	 * @return void
	 */
	public function onAfterFind($context, array &$row, $params = null)
	{
		if ($context !== 'users\services\Users::findByPk') {
			return ;
		}

		$userId = isset($row['user_id']) ? (int) $row['user_id'] : 0;
		if ($userId <= 0) {
			return ;
		}

		$db = Service::getInstance('Users', 'users')->getDb();
		$dbProxy = $db->getDbProxy();
		$tableName = $db->getTblprefix() . TableNames::getUserProfile();

		$profile = Profile::getInstance($tableName, $userId, $dbProxy);
		$attributes = $profile->findAll();
		if (is_array($attributes)) {
			$row['sex']           = isset($attributes['sex'])           ? $attributes['sex']           : DataUsers::SEX_UNKNOW;
			$row['birthday']      = isset($attributes['birthday'])      ? $attributes['birthday']      : '';
			$row['address']       = isset($attributes['address'])       ? $attributes['address']       : '';
			$row['qq']            = isset($attributes['qq'])            ? $attributes['qq']            : '';
			$row['head_portrait'] = isset($attributes['head_portrait']) ? $attributes['head_portrait'] : '';
			$row['remarks']       = isset($attributes['remarks'])       ? $attributes['remarks']       : '';
		}
	}

	/**
	 * 新增或编辑后执行
	 * @param string $context
	 * @param array $row
	 * @param mixed $params
	 * @return void
	 */
	public function onAfterSave($context, array &$row, $params = null)
	{
		$isCreate = ($context === 'users\services\Users::create')     ? true : false;
		$isModify = ($context === 'users\services\Users::modifyByPk') ? true : false;

		if (!$isCreate && !$isModify) {
			return ;
		}

		if (($userId = (int) $params) <= 0) {
			return ;
		}

		$sexEnum = DataUsers::getSexEnum();

		$sex          = isset($row['sex'])           ? trim($row['sex'])           : '';
		$birthday     = isset($row['birthday'])      ? trim($row['birthday'])      : '';
		$address      = isset($row['address'])       ? trim($row['address'])       : '';
		$qq           = isset($row['qq'])            ? (int) $row['qq']            : 0;
		$headPortrait = isset($row['head_portrait']) ? trim($row['head_portrait']) : '';
		$remarks      = isset($row['remarks'])       ? trim($row['remarks'])       : '';

		$sex          = (isset($sexEnum[$sex])       ? $sex : DataUsers::SEX_UNKNOW);
		$birthday     = (date('Y-m-d', strtotime($birthday)) === $birthday) ? $birthday : '';
		$qq           = ($qq > 0)                    ? $qq : '';

		$attributes = array();

		if ($isCreate) {
			$attributes = array(
				'sex'           => $sex,
				'birthday'      => $birthday,
				'address'       => $address,
				'qq'            => $qq,
				'head_portrait' => $headPortrait,
				'remarks'       => $remarks
			);
		}
		else {
			if (isset($row['sex'])) {
				$attributes['sex'] = $sex;
			}

			if (isset($row['birthday'])) {
				$attributes['birthday'] = $birthday;
			}

			if (isset($row['address'])) {
				$attributes['address'] = $address;
			}

			if (isset($row['qq'])) {
				$attributes['qq'] = $qq;
			}

			if (isset($row['head_portrait'])) {
				$attributes['head_portrait'] = $headPortrait;
			}

			if (isset($row['remarks'])) {
				$attributes['remarks'] = $remarks;
			}

			if ($attributes === array()) {
				return ;
			}
		}

		$db = Service::getInstance('Users', 'users')->getDb();
		$dbProxy = $db->getDbProxy();
		$tableName = $db->getTblprefix() . TableNames::getUserProfile();

		$profile = Profile::getInstance($tableName, $userId, $dbProxy);
		$profile->save($attributes);
	}
}
