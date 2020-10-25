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

use libapp\BaseModel;
use tfc\saf\Text;
use libapp\ErrorNo;
use member\services\DataAccount;
use member\services\Account AS SrvAccount;

/**
 * Account class file
 * 会员账户管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Account.php 1 2014-08-08 14:05:27Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Account extends BaseModel
{
	/**
	 * @var srv\srvname\services\classname 业务处理类
	 */
	protected $_service = null;

	/**
	 * 构造方法：初始化数据库操作类
	 */
	public function __construct()
	{
		$this->_service = new SrvAccount();
	}

	/**
	 * 会员登录
	 * @param string $loginName
	 * @param string $password
	 * @param boolean $rememberMe
	 * @return array
	 */
	public function login($loginName, $password, $rememberMe = false)
	{
		$ret = $this->_service->loginByNamePwd($loginName, $password, $rememberMe);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			$ret['data'] = array(
				'login_name' => $loginName,
				'password' => $password,
				'remember_me' => $rememberMe
			);

			return $ret;
		}

		$memberId = isset($ret['data']['member_id']) ? (int) $ret['data']['member_id'] : 0;
		$loginName = isset($ret['data']['login_name']) ? $ret['data']['login_name'] : '';
		$ret['data'] = array(
			'member_id' => $memberId,
			'login_name' => $loginName,
			'password' => $password,
			'remember_me' => $rememberMe
		);

		$ret['err_msg'] = Text::_('MOD_MEMBER_LOGIN_LOGIN_SUCCESS_HINT');
		return $ret;
	}

	/**
	 * 第三方账号登录
	 * @param string $partner
	 * @param string $openid
	 * @return array
	 */
	public function extlogin($partner, $openid)
	{
		$ret = $this->_service->loginByPartner($partner, $openid);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			$ret['data'] = array(
				'partner' => $partner,
				'openid' => $openid,
			);

			return $ret;
		}

		$memberId = isset($ret['data']['member_id']) ? (int) $ret['data']['member_id'] : 0;
		$loginName = isset($ret['data']['login_name']) ? $ret['data']['login_name'] : '';
		$ret['data'] = array(
			'member_id' => $memberId,
			'login_name' => $loginName,
			'remember_me' => false
		);

		$ret['err_msg'] = Text::_('MOD_MEMBER_LOGIN_LOGIN_SUCCESS_HINT');
		return $ret;
	}

	/**
	 * 注销账户
	 * @return boolean
	 */
	public function logout()
	{
		$this->_service->logout();
	}

	/**
	 * 会员注册
	 * @param string $loginName
	 * @param string $password
	 * @param string $repassword
	 * @return array
	 */
	public function register($loginName, $password, $repassword)
	{
		$params = array(
			'login_name' => $loginName,
			'password' => $password,
			'repassword' => $repassword
		);

		$ret = $this->callCreateMethod($this->_service->getPortal(), 'create', $params);
		if ($ret['err_no'] === ErrorNo::SUCCESS_NUM) {
			$ret['err_msg'] = Text::_('MOD_MEMBER_REGISTER_REG_SUCCESS_HINT');
		}

		return $ret;
	}
}
