<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\users\model;

use library\BaseModel;
use tfc\saf\Text;
use libapp\Model;
use users\services\DataGroups;
use library\ErrorNo;

/**
 * Groups class file
 * 用户组
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Groups.php 1 2014-05-30 11:00:05Z Code Generator $
 * @package modules.users.model
 * @since 1.0
 */
class Groups extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$output = array(
			'group_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_USERS_USER_GROUPS_GROUP_ID_LABEL'),
				'hint' => Text::_('MOD_USERS_USER_GROUPS_GROUP_ID_HINT'),
			),
			'group_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USER_GROUPS_GROUP_NAME_LABEL'),
				'hint' => Text::_('MOD_USERS_USER_GROUPS_GROUP_NAME_HINT'),
				'required' => true,
			),
			'group_pid' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_USERS_USER_GROUPS_GROUP_PID_LABEL'),
				'hint' => Text::_('MOD_USERS_USER_GROUPS_GROUP_PID_HINT'),
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USER_GROUPS_SORT_LABEL'),
				'hint' => Text::_('MOD_USERS_USER_GROUPS_SORT_HINT'),
				'required' => true,
			),
			'permission' => array(
				'__tid__' => 'main',
				'type' => 'checkbox',
				'label' => Text::_('MOD_USERS_USER_GROUPS_PERMISSION_LABEL'),
				'hint' => Text::_('MOD_USERS_USER_GROUPS_PERMISSION_HINT'),
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_USERS_USER_GROUPS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_USERS_USER_GROUPS_DESCRIPTION_HINT'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“组名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getGroupNameLink($data)
	{
		$params = array(
			'id' => $data['group_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['group_name'], $url);
		return $output;
	}

	/**
	 * 获取所有的组Id
	 * @return array
	 */
	public function getGroupIds()
	{
		return $this->getService()->getGroupIds();
	}

	/**
	 * 递归方式获取所有的组，默认用|—填充子组名左边用于和父组名错位（可用于Table列表）
	 * @param integer $groupPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function findLists($groupPid = 0, $padStr = '|—', $leftPad = '', $rightPad = null)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findLists', array($groupPid, $padStr, $leftPad, $rightPad));
		return $ret;
	}

	/**
	 * 递归方式获取所有的组名，默认用空格填充子组名左边用于和父组名错位
	 * （只返回ID和组名的键值对）（可用于Select表单的Option选项）
	 * @param integer $groupPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function getOptions($groupPid = 0, $padStr = '&nbsp;&nbsp;&nbsp;&nbsp;', $leftPad = '', $rightPad = null)
	{
		return $this->getService()->getOptions($groupPid, $padStr, $leftPad, $rightPad);
	}

	/**
	 * 通过主键，获取组名，并依次获取上级组名
	 * @param integer $groupId
	 * @return array
	 */
	public function getBreadcrumbs($groupId)
	{
		$breadcrumbs = array();

		$groupNames = $this->getService()->getGroupNames($groupId);
		foreach ($groupNames as $groupId => $groupName) {
			$breadcrumbs[] = array(
				'label' => $groupName,
				'href' => $this->urlManager->getUrl('permissionmodify', 'groups', 'users', array('id' => $groupId))
			);
		}

		return $breadcrumbs;
	}

	/**
	 * 获取所有的事件，并选中有权限的事件
	 * @param integer $groupId
	 * @return array
	 */
	public function getAmcas($groupId)
	{
		$ret = Model::getInstance('Amcas')->findAllByRecur();
		if ($ret['err_no'] !== ErrorNo::SUCCESS_NUM) {
			return $ret;
		}

		// 获取当前组所有的权限（当前权限、父级权限、父父级权限等），并将权限去重
		$permissions = $this->getService()->getPermissionByGroupId($groupId);

		// 获取父组所有的权限（当前权限、父级权限、父父级权限等），并将权限去重
		$parentPermissions = $this->getService()->getPermissions($this->getGroupPidByGroupId($groupId));

		$powers = DataGroups::getPowerEnum();
		foreach ($ret['data'] as $appName => $apps) {
			foreach ($apps['rows'] as $modName => $mods) {
				foreach ($mods['rows'] as $ctrlName => $ctrls) {
					$ret['data'][$appName]['rows'][$modName]['rows'][$ctrlName]['powers'] = $powers;
					if (isset($permissions[$appName][$modName][$ctrlName])) {
						$ret['data'][$appName]['rows'][$modName]['rows'][$ctrlName]['checked'] = $permissions[$appName][$modName][$ctrlName];
					}

					if (isset($parentPermissions[$appName][$modName][$ctrlName])) {
						$ret['data'][$appName]['rows'][$modName]['rows'][$ctrlName]['pchecked'] = $parentPermissions[$appName][$modName][$ctrlName];
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * 通过主键，获取权限，并递归获取父级权限、父父级权限等
	 * @param integer $groupId
	 * @return array
	 */
	public function getPermissions($groupId)
	{
		$ret = $this->callFetchMethod($this->getService(), 'getPermissions', array($groupId));
		return $ret;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $groupId
	 * @return array
	 */
	public function findByPk($groupId)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findByPk', array($groupId));
		return $ret;
	}

	/**
	 * 通过“主键ID”，获取“组名”
	 * @param integer $groupId
	 * @return string
	 */
	public function getGroupNameByGroupId($groupId)
	{
		return $this->getService()->getGroupNameByGroupId($groupId);
	}

	/**
	 * 通过“主键ID”，获取“父ID”
	 * @param integer $groupId
	 * @return integer
	 */
	public function getGroupPidByGroupId($groupId)
	{
		return $this->getService()->getGroupPidByGroupId($groupId);
	}

	/**
	 * 通过“主键ID”，获取“排序”
	 * @param integer $groupId
	 * @return integer
	 */
	public function getSortByGroupId($groupId)
	{
		return $this->getService()->getSortByGroupId($groupId);
	}

	/**
	 * 通过“主键ID”，获取“权限设置”
	 * @param integer $groupId
	 * @return array
	 */
	public function getPermissionByGroupId($groupId)
	{
		return $this->getService()->getPermissionByGroupId($groupId);
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $groupId
	 * @return string
	 */
	public function getDescriptionByGroupId($groupId)
	{
		return $this->getService()->getDescriptionByGroupId($groupId);
	}

	/**
	 * 通过主键，编辑“权限设置”
	 * @param integer $groupId
	 * @param array $params
	 * @return array
	 */
	public function modifyPermissionByPk($groupId, array $params)
	{
		$ret = $this->callModifyMethod($this->getService(), 'modifyPermissionByPk', $groupId, $params);
		return $ret;
	}
}
