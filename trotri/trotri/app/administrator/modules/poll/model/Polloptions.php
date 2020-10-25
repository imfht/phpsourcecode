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
use tfc\ap\Ap;
use tfc\saf\Text;
use libapp\Model;

/**
 * Polloptions class file
 * 投票选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polloptions.php 1 2014-12-06 22:30:41Z Code Generator $
 * @package modules.poll.model
 * @since 1.0
 */
class Polloptions extends BaseModel
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
			'option_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POLL_POLLOPTIONS_OPTION_ID_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLOPTIONS_OPTION_ID_HINT'),
			),
			'option_name' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POLL_POLLOPTIONS_OPTION_NAME_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLOPTIONS_OPTION_NAME_HINT'),
				'required' => true,
			),
			'poll_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POLL_POLLOPTIONS_POLL_ID_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLOPTIONS_POLL_ID_HINT'),
			),
			'votes' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLOPTIONS_VOTES_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLOPTIONS_VOTES_HINT'),
				'value' => 0
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POLL_POLLOPTIONS_SORT_LABEL'),
				'hint' => Text::_('MOD_POLL_POLLOPTIONS_SORT_HINT'),
				'required' => true,
			),
			'poll_name' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => Text::_('MOD_POLL_POLLS_POLL_NAME_LABEL'),
				'hint' => '',
			),
			'poll_key' => array(
				'__tid__' => 'main',
				'type' => 'string',
				'label' => Text::_('MOD_POLL_POLLS_POLL_KEY_LABEL'),
				'hint' => '',
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“选项”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getOptionNameLink($data)
	{
		$params = array(
			'id' => $data['option_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['option_name'], $url);
		return $output;
	}

	/**
	 * 获取poll_id值
	 * @return integer
	 */
	public function getPollId()
	{
		$pollId = Ap::getRequest()->getInteger('poll_id');
		if ($pollId <= 0) {
			$id = Ap::getRequest()->getInteger('id');
			$pollId = $this->getService()->getPollIdByOptionId($id);
		}

		return $pollId;
	}

	/**
	 * 通过“投票ID”，获取所有的选项
	 * @param integer $pollId
	 * @return array
	 */
	public function findAllByPollId($pollId)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findAllByPollId', array($pollId));
		return $ret;
	}

	/**
	 * 通过“投票ID”，获取“投票名”
	 * @param integer $pollId
	 * @return string
	 */
	public function getPollNameByPollId($pollId)
	{
		return Model::getInstance('Polls')->getPollNameByPollId($pollId);
	}

	/**
	 * 通过“投票ID”，获取“投票Key”
	 * @param integer $pollId
	 * @return string
	 */
	public function getPollKeyByPollId($pollId)
	{
		return Model::getInstance('Polls')->getPollKeyByPollId($pollId);
	}

}
