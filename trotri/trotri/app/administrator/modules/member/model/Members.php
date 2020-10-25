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
use tfc\auth\Identity;
use libapp\Model;
use libapp\ErrorNo;
use libapp\Lang;
use member\services\DataMembers;
use member\services\DataPortal;

/**
 * Members class file
 * 会员账户
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Members.php 1 2014-11-27 17:10:30Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Members extends BaseModel
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
			'member_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MEMBER_MEMBERS_MEMBER_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_MEMBER_ID_HINT'),
			),
			'login_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_LOGIN_NAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_LOGIN_NAME_HINT'),
				'required' => true,
			),
			'login_type' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_LOGIN_TYPE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_LOGIN_TYPE_HINT'),
				'options' => DataPortal::getLoginTypeEnum(),
				'value' => DataPortal::LOGIN_TYPE_MAIL,
			),
			'p_password' => array(
				'__tid__' => 'main',
				'type' => 'password',
				'label' => Text::_('MOD_MEMBER_MEMBERS_P_PASSWORD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_P_PASSWORD_HINT'),
				'required' => true,
			),
			'p_repassword' => array(
				'__tid__' => 'main',
				'type' => 'password',
				'label' => Text::_('MOD_MEMBER_MEMBERS_P_REPASSWORD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_P_REPASSWORD_HINT'),
				'required' => true,
			),
			'type_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBERS_TYPE_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_TYPE_ID_HINT'),
			),
			'rank_id' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_MEMBER_MEMBERS_RANK_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_RANK_ID_HINT'),
			),
			'experience' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_EXPERIENCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_EXPERIENCE_HINT'),
			),
			'balance' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_BALANCE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_BALANCE_HINT'),
			),
			'balance_freeze' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_BALANCE_FREEZE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_BALANCE_FREEZE_HINT'),
			),
			'points' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_POINTS_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_POINTS_HINT'),
			),
			'points_freeze' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_POINTS_FREEZE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_POINTS_FREEZE_HINT'),
			),
			'consum' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_CONSUM_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_CONSUM_HINT'),
			),
			'orders' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_ORDERS_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_ORDERS_HINT'),
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_MEMBER_MEMBERS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_DESCRIPTION_HINT'),
			),
			'dt_last_rerank' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_DT_LAST_RERANK_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_DT_LAST_RERANK_HINT'),
				'disabled' => true,
			),
			'dt_created' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBERS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBERS_DT_CREATED_HINT'),
				'disabled' => true,
			),
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::findByPk()
	 */
	public function findByPk($value)
	{
		$ret = parent::findByPk($value);
		if (isset($ret['data']) && is_array($ret['data'])) {
			if (isset($ret['data']['p_password'])) {
				$ret['data']['p_password'] = '';
			}
		}

		return $ret;
	}

	/**
	 * 获取所有的成长度名称
	 * @return array
	 */
	public function getRankNames()
	{
		return Model::getInstance('Ranks')->getRankNames();
	}

	/**
	 * 获取所有的类型名称
	 * @return array
	 */
	public function getTypeNames()
	{
		return Model::getInstance('Types')->getTypeNames();
	}

	/**
	 * 通过“会员成长度ID”，获取“会员成长度名”
	 * @param integer $rankId
	 * @return string
	 */
	public function getRankNameByRankId($rankId)
	{
		return Model::getInstance('Ranks')->getRankNameByRankId($rankId);
	}

	/**
	 * 通过“会员类型ID”，获取“会员类型名”
	 * @param integer $typeId
	 * @return string
	 */
	public function getTypeNameByTypeId($typeId)
	{
		return Model::getInstance('Types')->getTypeNameByTypeId($typeId);
	}

	/**
	 * 操作会员账户
	 * @param string $columnName
	 * @param string $opType
	 * @param integer $memberId
	 * @param integer|float $value
	 */
	public function opAccount($columnName, $opType, $memberId, $value)
	{
		$funcName = 'op' . ucfirst(strtolower($columnName));
		$ret = $this->getService()->$funcName($opType, $memberId, $value, DataMembers::SOURCE_ADMINOP, '', Identity::getUserId());

		if ($ret) {
			$errNo = ErrorNo::SUCCESS_NUM;
			$errMsg = Lang::_('ERROR_MSG_SUCCESS_UPDATE');
		}
		else {
			$errNo = ErrorNo::ERROR_DB_UPDATE;
			$errMsg = Lang::_('ERROR_MSG_ERROR_DB_UPDATE');
		}

		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
		);
	}
}
