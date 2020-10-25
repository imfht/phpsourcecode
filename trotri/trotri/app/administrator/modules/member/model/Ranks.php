<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\model;

use library\BaseModel;
use tfc\saf\Text;

/**
 * Ranks class file
 * 会员成长度
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ranks.php 1 2014-11-26 14:16:14Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Ranks extends BaseModel
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
			'rank_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MEMBER_MEMBER_RANKS_RANK_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_RANKS_RANK_ID_HINT'),
			),
			'rank_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_RANKS_RANK_NAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_RANKS_RANK_NAME_HINT'),
				'required' => true,
			),
			'experience' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_RANKS_EXPERIENCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_RANKS_EXPERIENCE_HINT'),
				'required' => true,
				'value' => 1000
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_RANKS_SORT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_RANKS_SORT_HINT'),
				'required' => true,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MEMBER_MEMBER_RANKS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_RANKS_DESCRIPTION_HINT'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“成长度名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getRankNameLink($data)
	{
		$params = array(
			'id' => $data['rank_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['rank_name'], $url);
		return $output;
	}

	/**
	 * 获取所有的成长度
	 * @return array
	 */
	public function findAll()
	{
		$ret = $this->callFetchMethod($this->getService(), 'findAll');
		return $ret;
	}

	/**
	 * 获取所有的成长度名称
	 * @return array
	 */
	public function getRankNames()
	{
		return $this->getService()->getRankNames();
	}

	/**
	 * 通过“主键ID”，获取“成长度名”
	 * @param integer $rankId
	 * @return string
	 */
	public function getRankNameByRankId($rankId)
	{
		return $this->getService()->getRankNameByRankId($rankId);
	}
}
