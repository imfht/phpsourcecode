<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace builders\services;

use libsrv\DynamicService;

/**
 * Groups class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Groups.php 1 2014-05-27 16:15:35Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class Groups extends DynamicService
{
	/**
	 * @var string 表名
	 */
	protected $_tableName = 'builder_field_groups';

	/**
	 * 通过builder_id获取所有的Prompts
	 * @param integer $builderId
	 * @param boolean $joinDafault
	 * @return array
	 */
	public function getPromptsByBuilderId($builderId, $joinDafault = false)
	{
		$default = array();
		$groups = array();

		if ($joinDafault) {
			$default = (array) $this->findPairsByAttributes(array('group_id', 'prompt'), array('builder_id' => 0), 'sort');
		}

		$groups = (array) $this->findPairsByAttributes(array('group_id', 'prompt'), array('builder_id' => (int) $builderId), 'sort');

		$ret = $default + $groups;
		return $ret;
	}

	/**
	 * 通过“字段组ID”获取“字段组名”
	 * @param integer $groupId
	 * @return string
	 */
	public function getGroupNameByGroupId($groupId)
	{
		$groupName = $this->getByPk('group_name', $groupId);
		return $groupName ? $groupName : '';
	}

	/**
	 * 通过“字段组ID”获取“字段组提示”
	 * @param integer $groupId
	 * @return string
	 */
	public function getPromptByGroupId($groupId)
	{
		$prompt = $this->getByPk('prompt', $groupId);
		return $prompt ? $prompt : '';
	}
}
