<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\poll\model;

use library\BaseModel;
use tfc\saf\Text;
use poll\services\DataPolls;
use member\services\DataMembers;

/**
 * Polls class file
 * 投票管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polls.php 1 2014-12-05 18:24:23Z Code Generator $
 * @package modules.poll.model
 * @since 1.0
 */
class Polls extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'system' => array(
				'tid' => 'system',
				'prompt' => Text::_('MOD_POLL_POLLS_VIEWTAB_SYSTEM_PROMPT')
			),
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$nowTime = date('Y-m-d H:i:s');
		$output = array(
			'poll_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POLL_POLLS_POLL_ID_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_POLL_ID_HINT'),
			),
			'poll_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLS_POLL_NAME_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_POLL_NAME_HINT'),
				'required' => true,
			),
			'poll_key' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLS_POLL_KEY_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_POLL_KEY_HINT'),
				'required' => true,
			),
			'allow_unregistered' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POLL_POLLS_ALLOW_UNREGISTERED_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_ALLOW_UNREGISTERED_HINT'),
				'options' => DataPolls::getAllowUnregisteredEnum(),
				'value' => DataPolls::ALLOW_UNREGISTERED_Y,
			),
			'm_rank_ids' => array(
				'__tid__' => 'main',
				'type' => 'checkbox',
				'label' => Text::_('MOD_POLL_POLLS_M_RANK_IDS_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_M_RANK_IDS_HINT'),
				'options' => DataMembers::getRanksEnum()
			),
			'join_type' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_POLL_POLLS_JOIN_TYPE_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_JOIN_TYPE_HINT'),
				'options' => DataPolls::getJoinTypeEnum(),
				'value' => DataPolls::JOIN_TYPE_FOREVER,
			),
			'interval' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLS_INTERVAL_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_INTERVAL_HINT'),
				'required' => true,
				'value' => 0
			),
			'is_published' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POLL_POLLS_IS_PUBLISHED_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_IS_PUBLISHED_HINT'),
				'options' => DataPolls::getIsPublishedEnum(),
				'value' => DataPolls::IS_PUBLISHED_Y,
			),
			'dt_publish_up' => array(
				'__tid__' => 'main',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_POLL_POLLS_DT_PUBLISH_UP_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_DT_PUBLISH_UP_HINT'),
				'value' => $nowTime
			),
			'dt_publish_down' => array(
				'__tid__' => 'main',
				'type' => 'datetimepicker',
				'label' => Text::_('MOD_POLL_POLLS_DT_PUBLISH_DOWN_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_DT_PUBLISH_DOWN_HINT'),
			),
			'is_visible' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POLL_POLLS_IS_VISIBLE_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_IS_VISIBLE_HINT'),
				'options' => DataPolls::getIsVisibleEnum(),
				'value' => DataPolls::IS_VISIBLE_Y,
			),
			'is_multiple' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POLL_POLLS_IS_MULTIPLE_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_IS_MULTIPLE_HINT'),
				'options' => DataPolls::getIsMultipleEnum(),
				'value' => DataPolls::IS_MULTIPLE_Y,
			),
			'max_choices' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLS_MAX_CHOICES_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_MAX_CHOICES_HINT'),
				'required' => true,
				'value' => 0
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POLL_POLLS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_DESCRIPTION_HINT'),
			),
			'ext_info' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POLL_POLLS_EXT_INFO_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_EXT_INFO_HINT'),
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLS_DT_CREATED_HINT'),
				'disabled' => true,
			),
			'polloptions' => array(
				'label' => Text::_('MOD_POLL_URLS_POLLOPTIONS_INDEX'),
			)
		);

		return $output;
	}

	/**
	 * 获取列表页“投票名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getPollNameLink($data)
	{
		$params = array(
			'id' => $data['poll_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['poll_name'], $url);
		return $output;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $id
	 * @param array $params
	 * @return array
	 */
	public function modifyByPk($id, array $params = array())
	{
		if (!isset($params['m_rank_ids'])) {
			$params['m_rank_ids'] = array();
		}

		return parent::modifyByPk($id, $params);
	}

	/**
	 * 通过“主键ID”，获取“投票名”
	 * @param integer $pollId
	 * @return string
	 */
	public function getPollNameByPollId($pollId)
	{
		$ret = $this->getService()->getPollNameByPollId($pollId);
		return $ret;
	}

	/**
	 * 通过“主键ID”，获取“投票Key”
	 * @param integer $pollId
	 * @return string
	 */
	public function getPollKeyByPollId($pollId)
	{
		$ret = $this->getService()->getPollKeyByPollId($pollId);
		return $ret;
	}

	/**
	 * 获取“参与方式”
	 * @param string $joinType
	 * @return string
	 */
	public function getJoinTypeLangByJoinType($joinType)
	{
		$ret = $this->getService()->getJoinTypeLangByJoinType($joinType);
		return $ret;
	}

	/**
	 * 获取“是否展示结果”
	 * @param string $isVisible
	 * @return string
	 */
	public function getIsVisibleLangByIsVisible($isVisible)
	{
		$ret = $this->getService()->getIsVisibleLangByIsVisible($isVisible);
		return $ret;
	}

	/**
	 * 获取“是否多选”
	 * @param string $isMultiple
	 * @return string
	 */
	public function getIsMultipleLangByIsMultiple($isMultiple)
	{
		$ret = $this->getService()->getIsMultipleLangByIsMultiple($isMultiple);
		return $ret;
	}

	/**
	 * 获取“是否允许非会员参加”
	 * @param string $allowUnregistered
	 * @return string
	 */
	public function getAllowUnregisteredByAllowUnregistered($allowUnregistered)
	{
		$ret = $this->getService()->getAllowUnregisteredByAllowUnregistered($allowUnregistered);
		return $ret;
	}

}
