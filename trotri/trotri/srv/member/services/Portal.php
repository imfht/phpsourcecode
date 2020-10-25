<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use libsrv\AbstractService;
use tfc\util\String;
use libsrv\Clean;
use libsrv\FormProcessor;
use member\library\Constant;
use member\library\Lang;

/**
 * Portal class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Portal.php 1 2014-11-26 21:46:18Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class Portal extends AbstractService
{
	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		if (isset($params['ip_registered'])) {
			$ipRegistered = trim($params['ip_registered']); unset($params['ip_registered']);
			if ($ipRegistered !== '') {
				$ipRegistered = (strpos($ipRegistered, '.') !== false) ? Clean::ip2long($ipRegistered) : (int) $ipRegistered;
				if ($ipRegistered !== false) {
					$params['ip_registered'] = $ipRegistered;
				}
			}
		}

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findByPk($memberId)
	{
		$row = $this->getDb()->findByPk($memberId);
		return $row;
	}

	/**
	 * 通过登录名，查询一条记录
	 * @param string $loginName
	 * @return array
	 */
	public function findByLoginName($loginName)
	{
		$row = $this->getDb()->findByLoginName($loginName);
		return $row;
	}

	/**
	 * 通过主键，编辑“登录密码”
	 * @param integer $memberId
	 * @param string $password
	 * @param string $repassword
	 * @return integer
	 */
	public function modifyPasswordByPk($memberId, $password, $repassword)
	{
		$formProcessor = $this->getFormProcessor();
		if (($password = trim($password)) === '') {
			$formProcessor->addError('password', Lang::_('SRV_FILTER_REPWD_NEW_PASSWORD_NOTEMPTY'));
			return false;
		}

		if (!$formProcessor->run(FormProcessor::OP_UPDATE, array('password' => $password, 'repassword' => $repassword), $memberId)) {
			return false;
		}

		$attributes = $formProcessor->getValues();
		$rowCount = $this->getDb()->modifyByPk($formProcessor->id, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑多条记录
	 * @param array|integer $values
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk($values, array $params = array())
	{
		$rowCount = $this->getDb()->batchModifyByPk($values, $params);
		return $rowCount;
	}

	/**
	 * 通过主键，将一条记录移至回收站
	 * @param integer $value
	 * @return integer
	 */
	public function trashByPk($value)
	{
		return $this->batchModifyByPk($value, array('trash' => DataPortal::TRASH_Y));
	}

	/**
	 * 通过主键，将多条记录移至回收站
	 * @param array $values
	 * @return integer
	 */
	public function batchTrashByPk(array $values)
	{
		return $this->batchModifyByPk($values, array('trash' => DataPortal::TRASH_Y));
	}

	/**
	 * 通过主键，从回收站还原一条记录
	 * @param integer $value
	 * @return integer
	 */
	public function restoreByPk($value)
	{
		return $this->batchModifyByPk($value, array('trash' => DataPortal::TRASH_N));
	}

	/**
	 * 通过主键，将多条记录移至回收站
	 * @param array $values
	 * @return integer
	 */
	public function batchRestoreByPk(array $values)
	{
		return $this->batchModifyByPk($values, array('trash' => DataPortal::TRASH_N));
	}

	/**
	 * 通过“主键ID”，获取“登录名”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLoginNameByMemberId($memberId)
	{
		$value = $this->getByPk('login_name', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“登录方式”
	 * @param integer $memberId
	 * @return string
	 */
	public function getLoginTypeByMemberId($memberId)
	{
		$value = $this->getByPk('login_type', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“登录密码”
	 * @param integer $memberId
	 * @return string
	 */
	public function getPasswordByMemberId($memberId)
	{
		$value = $this->getByPk('password', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“随机附加混淆码”
	 * @param integer $memberId
	 * @return string
	 */
	public function getSaltByMemberId($memberId)
	{
		$value = $this->getByPk('salt', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“会员名”
	 * @param integer $memberId
	 * @return string
	 */
	public function getMemberNameByMemberId($memberId)
	{
		$value = $this->getByPk('member_name', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“邮箱”
	 * @param integer $memberId
	 * @return string
	 */
	public function getMemberMailByMemberId($memberId)
	{
		$value = $this->getByPk('member_mail', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“手机号”
	 * @param integer $memberId
	 * @return string
	 */
	public function getMemberPhoneByMemberId($memberId)
	{
		$value = $this->getByPk('member_phone', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“关联会员ID，用于合并账号”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getRelationMemberIdByMemberId($memberId)
	{
		$value = $this->getByPk('relation_member_id', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“注册时间”
	 * @param integer $memberId
	 * @return string
	 */
	public function getDtRegisteredByMemberId($memberId)
	{
		$value = $this->getByPk('dt_registered', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次登录时间”
	 * @param integer $memberId
	 * @return string
	 */
	public function getDtLastLoginByMemberId($memberId)
	{
		$value = $this->getByPk('dt_last_login', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次更新密码时间”
	 * @param integer $memberId
	 * @return string
	 */
	public function getDtLastRepwdByMemberId($memberId)
	{
		$value = $this->getByPk('dt_last_repwd', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“注册IP”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getIpRegisteredByMemberId($memberId)
	{
		$value = $this->getByPk('ip_registered', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“上次登录IP”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getIpLastLoginByMemberId($memberId)
	{
		$value = $this->getByPk('ip_last_login', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“上次更新密码IP”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getIpLastRepwdByMemberId($memberId)
	{
		$value = $this->getByPk('ip_last_repwd', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“总登录次数”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getLoginCountByMemberId($memberId)
	{
		$value = $this->getByPk('login_count', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“总更新密码次数”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getRepwdCountByMemberId($memberId)
	{
		$value = $this->getByPk('repwd_count', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“是否已验证邮箱”
	 * @param integer $memberId
	 * @return string
	 */
	public function getValidMailByMemberId($memberId)
	{
		$value = $this->getByPk('valid_mail', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否已验证手机号”
	 * @param integer $memberId
	 * @return string
	 */
	public function getValidPhoneByMemberId($memberId)
	{
		$value = $this->getByPk('valid_phone', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否禁用”
	 * @param integer $memberId
	 * @return string
	 */
	public function getForbiddenByMemberId($memberId)
	{
		$value = $this->getByPk('forbidden', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“是否删除”
	 * @param integer $memberId
	 * @return string
	 */
	public function getTrashByMemberId($memberId)
	{
		$value = $this->getByPk('trash', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 获取“是否已验证邮箱”
	 * @param string $validMail
	 * @return string
	 */
	public function getValidMailLangByValidMail($validMail)
	{
		$enum = DataPortal::getValidMailEnum();
		return isset($enum[$validMail]) ? $enum[$validMail] : '';
	}

	/**
	 * 获取“是否已验证手机号”
	 * @param string $validPhone
	 * @return string
	 */
	public function getValidPhoneLangByValidPhone($validPhone)
	{
		$enum = DataPortal::getValidPhoneEnum();
		return isset($enum[$validPhone]) ? $enum[$validPhone] : '';
	}

	/**
	 * 获取“是否禁用”
	 * @param string $forbidden
	 * @return string
	 */
	public function getForbiddenLangByForbidden($forbidden)
	{
		$enum = DataPortal::getForbiddenEnum();
		return isset($enum[$forbidden]) ? $enum[$forbidden] : '';
	}

	/**
	 * 获取用户登录随机附加混淆码
	 * @return string
	 */
	public function getSalt()
	{
		return String::randStr(6);
	}

	/**
	 * 加密用户登录密码
	 * @param string $pwd
	 * @param string $salt
	 * @return string
	 */
	public function encrypt($pwd, $salt = '')
	{
		return md5($salt . substr(md5($pwd), 3));
	}

	/**
	 * 通过登录名自动识别登录方式
	 * @param string $loginName
	 * @return string
	 */
	public function getLoginType($loginName)
	{
		if (strpos($loginName, '@')) {
			return DataPortal::LOGIN_TYPE_MAIL;
		}

		if (is_numeric($loginName)) {
			return DataPortal::LOGIN_TYPE_PHONE;
		}

		return DataPortal::LOGIN_TYPE_NAME;
	}

	/**
	 * 是否通过邮箱登录
	 * @param string $loginType
	 * @return boolean
	 */
	public function isMailLogin($loginType)
	{
		return $loginType === DataPortal::LOGIN_TYPE_MAIL;
	}

	/**
	 * 是否通过手机号登录
	 * @param string $loginType
	 * @return boolean
	 */
	public function isPhoneLogin($loginType)
	{
		return $loginType === DataPortal::LOGIN_TYPE_PHONE;
	}
}
