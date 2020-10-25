<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace users\services;

use libsrv\FormProcessor;
use tfc\ap\Ap;
use tfc\saf\Log;
use tfc\validator;
use libsrv\Clean;
use libapp\ErrorNo;
use users\library\Lang;
use users\library\TableNames;

/**
 * FpUsers class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpUsers.php 1 2014-08-07 10:09:58Z Code Generator $
 * @package users.services
 * @since 1.0
 */
class FpUsers extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			if (!$this->required($params, 'login_name', 'password', 'repassword')) {
				return false;
			}
		}

		$this->isValids($params,
			'login_type', 'login_name', 'password', 'repassword', 'salt', 'user_name', 'user_mail', 'user_phone',
			'dt_registered', 'dt_last_login', 'dt_last_repwd', 'ip_registered', 'ip_last_login', 'ip_last_repwd',
			'login_count', 'repwd_count', 'valid_mail', 'valid_phone', 'forbidden', 'trash', 'group_ids');

		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		if (isset($params['trash'])) { unset($params['trash']); }

		if ($this->isInsert()) {
			if (isset($params['salt'])) { unset($params['salt']); }
			if (isset($params['dt_last_repwd'])) { unset($params['dt_last_repwd']); }
			if (isset($params['ip_last_repwd'])) { unset($params['ip_last_repwd']); }
			if (isset($params['repwd_count'])) { unset($params['repwd_count']); }

			$params['dt_registered'] = $params['dt_last_login'] = date('Y-m-d H:i:s');
			$params['ip_registered'] = $params['ip_last_login'] = Clean::ip2long(Ap::getRequest()->getClientIp());
			$params['login_count'] = 1;
			$params['salt'] = $this->_object->getSalt();

			$params['login_name'] = $loginName = isset($params['login_name']) ? trim($params['login_name']) : '';
			$params['login_type'] = $loginType = $this->_object->getLoginType($loginName);

			if ($this->_object->isMailLogin($loginType)) {
				if (!isset($params['user_mail']) || trim($params['user_mail']) === '') {
					$params['user_mail'] = $loginName;
				}
			}
			elseif ($this->_object->isPhoneLogin($loginType)) {
				if (!isset($params['user_phone']) || trim($params['user_phone']) === '') {
					$params['user_phone'] = $loginName;
				}
			}

			if (!isset($params['user_name']) || trim($params['user_name']) === '') {
				if ($this->_object->isMailLogin($loginType)) {
					$params['user_name'] = strstr($loginName, '@', true);
				}
				else {
					$params['user_name'] = $loginName;
				}
			}

			if (!isset($params['group_ids'])) {
				$params['group_ids'] = array();
			}

			if (!is_array($params['group_ids'])) {
				$params['group_ids'] = (array) $params['group_ids'];
			}
		}
		else {
			$row = $this->_object->findByPk($this->id);
			if (!$row || !is_array($row) || !isset($row['repwd_count'])) {
				Log::warning(sprintf(
					'FpUsers is unable to find the result by id "%d"', $this->id
				), ErrorNo::ERROR_DB_SELECT,  __METHOD__);

				return false;
			}

			if (isset($params['login_name'])) { unset($params['login_name']); }
			if (isset($params['login_type'])) { unset($params['login_type']); }
			if (isset($params['salt'])) { unset($params['salt']); }
			if (isset($params['dt_registered'])) { unset($params['dt_registered']); }
			if (isset($params['ip_registered'])) { unset($params['ip_registered']); }

			$password = isset($params['password']) ? trim($params['password']) : '';
			if ($password !== '') {
				if (!isset($params['repassword'])) { $params['repassword'] = ''; }

				$params['salt'] = $this->_object->getSalt();
				$params['dt_last_repwd'] = date('Y-m-d H:i:s');
				$params['ip_last_repwd'] = Clean::ip2long(Ap::getRequest()->getClientIp());
				$params['repwd_count'] = (int) $row['repwd_count'] + 1;
			}
			else {
				if (isset($params['password'])) { unset($params['password']); }
				if (isset($params['repassword'])) { unset($params['repassword']); }
				if (isset($params['dt_last_repwd'])) { unset($params['dt_last_repwd']); }
				if (isset($params['ip_last_repwd'])) { unset($params['ip_last_repwd']); }
				if (isset($params['repwd_count'])) { unset($params['repwd_count']); }
			}

			if (isset($params['group_ids']) && !is_array($params['group_ids'])) {
				$params['group_ids'] = (array) $params['group_ids'];
			}
		}

		$rules = array(
			'login_name' => 'trim',
			'login_type' => 'trim',
			'password' => 'trim',
			'repassword' => 'trim',
			'salt' => 'trim',
			'user_name' => 'trim',
			'user_mail' => 'trim',
			'user_phone' => 'trim',
			'dt_registered' => 'trim',
			'dt_last_login' => 'trim',
			'dt_last_repwd' => 'trim',
			'ip_registered' => 'intval',
			'ip_last_login' => 'intval',
			'ip_last_repwd' => 'intval',
			'login_count' => 'intval',
			'repwd_count' => 'intval',
			'valid_mail' => 'trim',
			'valid_phone' => 'trim',
			'forbidden' => 'trim',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPostProcess()
	 */
	protected function _cleanPostProcess()
	{
		if (isset($this->password)) {
			if (isset($this->salt) && strlen($this->salt) > 0) {
				$this->password = $this->_object->encrypt($this->password, $this->salt);
			}
			else {
				return false;
			}
		}

		if (isset($this->repassword)) {
			unset($this->repassword);
		}

		return true;
	}

	/**
	 * 获取“登录名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getLoginNameRule($value)
	{
		$rules = array(
			'MinLength' => new validator\MinLengthValidator($value, 6, Lang::_('SRV_FILTER_USERS_LOGIN_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 18, Lang::_('SRV_FILTER_USERS_LOGIN_NAME_MAXLENGTH')),
		);

		if ($this->_object->isMailLogin($this->login_type)) {
			$rules['Mail'] = new validator\MailValidator($value, true, Lang::_('SRV_FILTER_USERS_LOGIN_NAME_MAIL'));
		}
		elseif ($this->_object->isPhoneLogin($this->login_type)) {
			$rules['Phone'] = new validator\PhoneValidator($value, true, Lang::_('SRV_FILTER_USERS_LOGIN_NAME_PHONE'));
		}
		else {
			$rules['AlphaNum'] = new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_USERS_LOGIN_NAME_ALPHANUM'));
		}

		$rules['DbExists'] = new validator\DbExistsValidator($value, false, Lang::_('SRV_FILTER_USERS_LOGIN_NAME_UNIQUE'), $this->getDbProxy(), TableNames::getUsers(), 'login_name');
		return $rules;
	}

	/**
	 * 获取“登录方式”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getLoginTypeRule($value)
	{
		$enum = DataUsers::getLoginTypeEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_USERS_LOGIN_TYPE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“登录密码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPasswordRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 6, Lang::_('SRV_FILTER_USERS_PASSWORD_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_USERS_PASSWORD_MAXLENGTH')),
		);
	}

	/**
	 * 获取“登录密码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getRepasswordRule($value)
	{
		return array(
			'Equal' => new validator\EqualValidator($value, $this->password, Lang::_('SRV_FILTER_USERS_REPASSWORD_EQUAL')),
		);
	}

	/**
	 * 获取“随机附加混淆码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getSaltRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_USERS_SALT_NOTEMPTY')),
		);
	}

	/**
	 * 获取“用户名”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getUserNameRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 4, Lang::_('SRV_FILTER_USERS_USER_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 50, Lang::_('SRV_FILTER_USERS_USER_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“邮箱”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getUserMailRule($value)
	{
		if ($value === '') { return array(); }

		return array(
			'Mail' => new validator\MailValidator($value, true, Lang::_('SRV_FILTER_USERS_USER_MAIL_MAIL')),
		);
	}

	/**
	 * 获取“手机号”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getUserPhoneRule($value)
	{
		if ($value === '') { return array(); }

		return array(
			'Phone' => new validator\PhoneValidator($value, true, Lang::_('SRV_FILTER_USERS_USER_PHONE_PHONE')),
		);
	}

	/**
	 * 获取“上次登录时间”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getDtLastLoginRule($value)
	{
		return array(
			'DateTime' => new validator\DateTimeValidator($value, true, Lang::_('SRV_FILTER_USERS_DT_LAST_LOGIN_DATETIME')),
		);
	}

	/**
	 * 获取“总登录次数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getLoginCountRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_USERS_LOGIN_COUNT_INTEGER')),
		);
	}

	/**
	 * 获取“是否已验证邮箱”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getValidMailRule($value)
	{
		$enum = DataUsers::getValidMailEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_USERS_VALID_MAIL_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否已验证手机号”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getValidPhoneRule($value)
	{
		$enum = DataUsers::getValidPhoneEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_USERS_VALID_PHONE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否禁用”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getForbiddenRule($value)
	{
		$enum = DataUsers::getForbiddenEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_USERS_FORBIDDEN_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否删除”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTrashRule($value)
	{
		$enum = DataUsers::getTrashEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_USERS_TRASH_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“用户分组ID”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getGroupIdsRule($value)
	{
		$enum = DataUsers::getGroupIdsEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, $enum, Lang::_('SRV_FILTER_USERS_GROUP_IDS_INARRAY')),
		);
	}
}
