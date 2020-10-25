<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\model;

use library\BaseModel;
use tfc\ap\Ap;
use tfc\saf\Text;
use libapp\Model;
use library\Constant;

/**
 * Groups class file
 * 字段组
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Groups.php 1 2014-05-27 16:15:35Z Code Generator $
 * @package modules.builder.model
 * @since 1.0
 */
class Groups extends BaseModel
{
	/**
	 * @var string 业务层名
	 */
	protected $_srvName = Constant::SRV_NAME_BUILDERS;

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
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_GROUP_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_GROUP_ID_HINT'),
			),
			'group_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_GROUP_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_GROUP_NAME_HINT'),
				'required' => true,
			),
			'prompt' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_PROMPT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_PROMPT_HINT'),
				'required' => true,
			),
			'builder_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_BUILDER_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_BUILDER_ID_HINT'),
				'required' => true,
			),
			'builder_name' => array(
				'type' => 'string',
				'label' => Text::_('MOD_BUILDER_BUILDERS_BUILDER_NAME_LABEL'),
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_SORT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_SORT_HINT'),
				'required' => true,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDER_FIELD_GROUPS_DESCRIPTION_HINT'),
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
	 * 获取builder_id值
	 * @return integer
	 */
	public function getBuilderId()
	{
		$builderId = Ap::getRequest()->getInteger('builder_id');
		if ($builderId <= 0) {
			$id = Ap::getRequest()->getInteger('id');
			$builderId = $this->getService()->getByPk('builder_id', $id);
		}

		return $builderId;
	}

	/**
	 * 通过“生成代码ID”获取“生成代码名”
	 * @param integer $builderId
	 * @return string
	 */
	public function getBuilderNameByBuilderId($builderId)
	{
		return Model::getInstance('Builders')->getBuilderNameByBuilderId($builderId);
	}

	/**
	 * 通过“字段组ID”获取“字段组名”
	 * @param integer $groupId
	 * @return string
	 */
	public function getGroupNameByGroupId($groupId)
	{
		return $this->getService()->getGroupNameByGroupId($groupId);
	}

	/**
	 * 通过“字段组ID”获取“字段组提示”
	 * @param integer $groupId
	 * @return string
	 */
	public function getPromptByGroupId($groupId)
	{
		return $this->getService()->getPromptByGroupId($groupId);
	}

	/**
	 * 通过builder_id获取所有的Prompts
	 * @param integer $builderId
	 * @param boolean $joinDafault
	 * @return array
	 */
	public function getPromptsByBuilderId($builderId, $joinDafault = false)
	{
		return $this->getService()->getPromptsByBuilderId($builderId, $joinDafault);
	}

	/**
	 * 查询数据列表
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function search(array $params = array(), $order = '', $limit = null, $offset = null)
	{
		$builderId = isset($params['builder_id']) ? (int) $params['builder_id'] : 0;
		$params = array();
		if ($builderId >= 0) {
			$params['builder_id'] = $builderId;
		}

		$ret = parent::search($params, '', $limit, $offset);
		return $ret;
	}
}
