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
use member\services\DataPortal;

/**
 * Portal class file
 * 会员账户
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Portal.php 1 2014-11-26 22:44:20Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Portal extends BaseModel
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
				'prompt' => Text::_('MOD_MEMBER_MEMBER_PORTAL_VIEWTAB_SYSTEM_PROMPT')
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
		$output = array(
			'member_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_ID_HINT'),
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
			'password' => array(
				'__tid__' => 'main',
				'type' => 'password',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_PASSWORD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_PASSWORD_HINT'),
				'required' => true,
			),
			'repassword' => array(
				'__tid__' => 'main',
				'type' => 'password',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_REPASSWORD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_REPASSWORD_HINT'),
				'required' => true,
			),
			'salt' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_SALT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_SALT_HINT'),
			),
			'member_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_NAME_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_NAME_HINT'),
			),
			'member_mail' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_MAIL_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_MAIL_HINT'),
			),
			'member_phone' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_PHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_MEMBER_PHONE_HINT'),
			),
			'relation_member_id' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_RELATION_MEMBER_ID_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_RELATION_MEMBER_ID_HINT'),
			),
			'dt_registered' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_DT_REGISTERED_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_DT_REGISTERED_HINT'),
				'disabled' => true,
			),
			'dt_last_login' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_DT_LAST_LOGIN_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_DT_LAST_LOGIN_HINT'),
				'disabled' => true,
			),
			'dt_last_repwd' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_DT_LAST_REPWD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_DT_LAST_REPWD_HINT'),
				'disabled' => true,
			),
			'ip_registered' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_IP_REGISTERED_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_IP_REGISTERED_HINT'),
				'disabled' => true,
			),
			'ip_last_login' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_IP_LAST_LOGIN_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_IP_LAST_LOGIN_HINT'),
				'disabled' => true,
			),
			'ip_last_repwd' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_IP_LAST_REPWD_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_IP_LAST_REPWD_HINT'),
				'disabled' => true,
			),
			'login_count' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_LOGIN_COUNT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_LOGIN_COUNT_HINT'),
				'disabled' => true,
			),
			'repwd_count' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_REPWD_COUNT_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_REPWD_COUNT_HINT'),
				'disabled' => true,
			),
			'valid_mail' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_VALID_MAIL_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_VALID_MAIL_HINT'),
				'options' => DataPortal::getValidMailEnum(),
				'value' => DataPortal::VALID_MAIL_N,
			),
			'valid_phone' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_VALID_PHONE_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_VALID_PHONE_HINT'),
				'options' => DataPortal::getValidPhoneEnum(),
				'value' => DataPortal::VALID_PHONE_N,
			),
			'forbidden' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_FORBIDDEN_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_FORBIDDEN_HINT'),
				'options' => DataPortal::getForbiddenEnum(),
				'value' => DataPortal::FORBIDDEN_N,
			),
			'trash' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_MEMBER_MEMBER_PORTAL_TRASH_LABEL'),
				'hint' => Text::_('MOD_MEMBER_MEMBER_PORTAL_TRASH_HINT'),
				'options' => DataPortal::getTrashEnum(),
				'value' => DataPortal::TRASH_N,
			),
			'dt_registered_ge' => array(
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USERS_DT_REGISTERED_GE_LABEL'),
			),
			'dt_registered_le' => array(
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USERS_DT_REGISTERED_LE_LABEL'),
			),
			'dt_last_login_ge' => array(
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USERS_DT_LAST_LOGIN_GE_LABEL'),
			),
			'dt_last_login_le' => array(
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USERS_DT_LAST_LOGIN_LE_LABEL'),
			),
			'login_count_ge' => array(
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USERS_LOGIN_COUNT_GE_LABEL'),
			),
			'login_count_le' => array(
				'type' => 'text',
				'label' => Text::_('MOD_USERS_USERS_LOGIN_COUNT_LE_LABEL'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“登录名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getLoginNameLink($data)
	{
		$params = array(
			'id' => $data['member_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['login_name'], $url);
		return $output;
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
		if (isset($params['member_name']) && $params['member_name'] === '') {
			unset($params['member_name']);
		}

		if (isset($params['member_mail']) && $params['member_mail'] === '') {
			unset($params['member_mail']);
		}

		if (isset($params['member_phone']) && $params['member_phone'] === '') {
			unset($params['member_phone']);
		}

		return parent::search($params, $order, $limit, $offset);
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::findByPk()
	 */
	public function findByPk($value)
	{
		$ret = parent::findByPk($value);
		if (isset($ret['data']) && is_array($ret['data'])) {
			if (isset($ret['data']['ip_registered'])) {
				$ret['data']['ip_registered'] = long2ip($ret['data']['ip_registered']);
			}

			if (isset($ret['data']['ip_last_login'])) {
				$ret['data']['ip_last_login'] = long2ip($ret['data']['ip_last_login']);
			}

			if (isset($ret['data']['ip_last_repwd'])) {
				$ret['data']['ip_last_repwd'] = long2ip($ret['data']['ip_last_repwd']);
			}

			if (isset($ret['data']['password'])) {
				$ret['data']['password'] = '';
			}
		}

		return $ret;
	}

	/**
	 * 通过“主键ID”，获取“登录名”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLoginNameByMemberId($memberId)
	{
		$ret = $this->getService()->getLoginNameByMemberId($memberId);
		return $ret;
	}

	/**
	 * 获取“是否已验证邮箱”
	 * @param string $validMail
	 * @return string
	 */
	public function getValidMailLangByValidMail($validMail)
	{
		$ret = $this->getService()->getValidMailLangByValidMail($validMail);
		return $ret;
	}

	/**
	 * 获取“是否已验证手机号”
	 * @param string $validPhone
	 * @return string
	 */
	public function getValidPhoneLangByValidPhone($validPhone)
	{
		$ret = $this->getService()->getValidPhoneLangByValidPhone($validPhone);
		return $ret;
	}

	/**
	 * 获取“是否禁用”
	 * @param string $forbidden
	 * @return string
	 */
	public function getForbiddenLangByForbidden($forbidden)
	{
		$ret = $this->getService()->getForbiddenLangByForbidden($forbidden);
		return $ret;
	}
}
