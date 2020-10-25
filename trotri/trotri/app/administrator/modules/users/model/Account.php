<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\users\model;

use users\services\DataAccount;
use users\services\Account AS SrvAccount;

/**
 * Account class file
 * 用户账户管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Account.php 1 2014-08-08 14:05:27Z Code Generator $
 * @package modules.users.model
 * @since 1.0
 */
class Account
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
	 * 用户登录
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

		$userId = isset($ret['data']['user_id']) ? (int) $ret['data']['user_id'] : 0;
		$loginName = isset($ret['data']['login_name']) ? $ret['data']['login_name'] : '';
		$ret['data'] = array(
			'user_id' => $userId,
			'login_name' => $loginName,
			'password' => $password,
			'remember_me' => $rememberMe
		);

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
}
