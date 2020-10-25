<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\plugins\profile;

use tfc\ap\Event;
use tdo\Profile;
use libsrv\Service;
use posts\library\TableNames;

/**
 * PlgProfile class file
 * 管理文档扩展信息
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PlgProfile.php 1 2013-04-05 01:38:06Z huan.song $
 * @package posts.plugins.profile
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
		if ($context !== 'posts\services\Posts::findByPk') {
			return ;
		}

		$postId = isset($row['post_id']) ? (int) $row['post_id'] : 0;
		if ($postId <= 0) {
			return ;
		}

		$moduleId = isset($row['module_id']) ? (int) $row['module_id'] : 0;
		if ($moduleId <= 0) {
			return ;
		}

		$fields = Service::getInstance('Modules', 'posts')->getFieldsByModuleId($moduleId);
		if ($fields === array()) {
			return ;
		}

		$db = Service::getInstance('Posts', 'posts')->getDb();
		$dbProxy = $db->getDbProxy();
		$tableName = $db->getTblprefix() . TableNames::getPostProfile();

		$profile = Profile::getInstance($tableName, $postId, $dbProxy);
		$attributes = $profile->findAll();
		if (is_array($attributes)) {
			foreach ($fields as $name => $field) {
				$row[$name] = isset($attributes[$name]) ? $attributes[$name] : '';
			}
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
		$isCreate = ($context === 'posts\services\Posts::create')     ? true : false;
		$isModify = ($context === 'posts\services\Posts::modifyByPk') ? true : false;

		if (!$isCreate && !$isModify) {
			return ;
		}

		if (($postId = (int) $params) <= 0) {
			return ;
		}

		$service = Service::getInstance('Posts', 'posts');
		$fields = $service->getModuleFieldsByPostId($postId);
		if ($fields === array()) {
			return ;
		}

		$attributes = array();

		foreach ($fields as $name => $field) {
			if (isset($row[$name])) {
				$attributes[$name] = $row[$name];
			}
			else {
				if ($isCreate) {
					$attributes[$name] = '';
				}
			}
		}

		if ($attributes === array()) {
			return ;
		}

		$db = $service->getDb();
		$dbProxy = $db->getDbProxy();
		$tableName = $db->getTblprefix() . TableNames::getPostProfile();

		$profile = Profile::getInstance($tableName, $postId, $dbProxy);
		$profile->save($attributes);
	}
}
