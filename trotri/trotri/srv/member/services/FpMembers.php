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

use libsrv\FormProcessor;
use tfc\validator;
use libsrv\Service;
use member\library\Lang;

/**
 * FpMembers class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpMembers.php 1 2014-11-27 17:10:30Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class FpMembers extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		if ($this->isInsert()) {
			return false;
		}

		if (!$this->required($params, 'p_password', 'p_repassword')) {
			return false;
		}

		$this->isValids($params, 'p_password', 'p_repassword', 'p_salt');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		$params['p_salt'] = $this->_object->getSalt();

		$rules = array(
			'p_password' => 'trim',
			'p_repassword' => 'trim',
			'p_salt' => 'trim',
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
		if (isset($this->p_password)) {
			$portal = Service::getInstance('Portal', 'member');
			$row = $portal->findByPk($this->id);
			if (!$row || !is_array($row) || !isset($row['password']) || !isset($row['salt'])) {
				return false;
			}

			$pPassword = $portal->encrypt($this->p_password, $row['salt']);
			if ($pPassword === $row['password']) {
				$this->addError('p_password', Lang::_('SRV_FILTER_MEMBERS_P_PASSWORD_EQUAL'));
				return false;
			}
		}

		if (isset($this->p_password)) {
			if (isset($this->p_salt) && strlen($this->p_salt) > 0) {
				$this->p_password = $this->_object->encrypt($this->p_password, $this->p_salt);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}

		if (isset($this->p_repassword)) {
			unset($this->p_repassword);
		}

		return true;
	}

	/**
	 * 获取“支付密码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPPasswordRule($value)
	{
		return array(
			'MinLength' => new validator\MinLengthValidator($value, 6, Lang::_('SRV_FILTER_MEMBERS_P_PASSWORD_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 20, Lang::_('SRV_FILTER_MEMBERS_P_PASSWORD_MAXLENGTH')),
		);
	}

	/**
	 * 获取“确认密码”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getPRepasswordRule($value)
	{
		return array(
			'Equal' => new validator\EqualValidator($value, $this->p_password, Lang::_('SRV_FILTER_MEMBERS_P_REPASSWORD_EQUAL')),
		);
	}
}
