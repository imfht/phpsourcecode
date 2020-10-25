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

use tfc\ap\Ap;
use tfc\saf\Cfg;
use tfc\saf\Log;
use tfc\auth\Authentica;
use tfc\auth\Authoriz;
use tfc\auth\Role;
use tfc\auth\Identity;
use libsrv\Clean;

/**
 * Account class file
 * 业务层：业务处理类
 * <pre>
 * 配置 /cfg/app/appname/main.php：
 * return array (
 *   'account' => array (
 *     'key_name' => 'auth_administrator',      // 密钥配置名
 *     'domain' => '',                          // Cookie的有效域名，缺省：当前域名
 *     'path' => '/',                           // Cookie的有效服务器路径，缺省：/
 *     'secure' => false,                       // FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输，缺省：FALSE
 *     'httponly' => true,                      // TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击，缺省：TRUE
 *     'expiry' => WEEK_IN_SECONDS,             // 记住密码时间
 *     'cookie_name' => 'atrid',                // Cookie名
 *     'cookset_password' => false,             // Cookie中设置密码
 *     'cookset_rolenames' => true,             // Cookie中设置用户拥有的角色名
 *     'cookset_appnames' => true,              // Cookie中设置用户拥有权限的项目名
 *   ),
 * )
 *
 * 配置 /cfg/key/cluster.php：
 * return array (
 *   'auth_administrator' => array (
 *     'crypt' => 'UViRN53uj7yZ5IAfdIGiq5bvRuCH9njd', // 加密密钥
 *     'sign' => 'xwFVMiM98nzW6PwW9jxCmT2mLTv5IJES',  // 签名密钥
 *     'expiry' => MONTH_IN_SECONDS,                  // 缺省的密文有效期，如果等于0，表示永久有效，单位：秒
 *     'rnd_len' => 20                                // 随机密钥长度，取值 0-32
 *   ),
 * )
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Account.php 1 2014-08-07 10:09:58Z huan.song $
 * @package users.services
 * @since 1.0
 */
class Account
{
	/**
	 * @var string Cookie配置名
	 */
	const CLUSTER_NAME = 'account';

	/**
	 * @var instance of users\services\Users
	 */
	protected $_users = null;

	/**
	 * @var instance of users\services\Groups
	 */
	protected $_groups = null;

	/**
	 * 构造方法：初始化业务处理类
	 */
	public function __construct()
	{
		$this->_users = new Users();
		$this->_groups = new Groups();
	}

	/**
	 * 获取用户身份授权类
	 * @param array $groupIds
	 * @return tfc\auth\Authoriz
	 */
	public function getAuthoriz($groupIds)
	{
		$groupIds = (array) $groupIds;

		$temp = array();
		foreach ($groupIds as $groupId) {
			if (($groupId = (int) $groupId) > 0) {
				$temp[] = $groupId;
			}
		}

		$groupIds = array_unique($temp);

		$authoriz = new Authoriz();
		foreach ($groupIds as $groupId) {
			$role = new Role($groupId);
			if (!$role->fileExists()) {
				$permission = $this->_groups->getPermissions($groupId);
				if (is_array($permission)) {
					foreach ($permission as $appName => $mods) {
						if (is_array($mods)) {
							foreach ($mods as $modName => $ctrls) {
								if (is_array($ctrls)) {
									foreach ($ctrls as $ctrlName => $powers) {
										if (is_array($powers)) {
											foreach ($powers as $powerName) {
												$role->allow($appName, $modName, $ctrlName, $powerName);
											}
										}
									}
								}
							}
						}
					}
				}

				$role->writeResources()->loadResources();
			}

			$authoriz->addRole($role);
		}

		return $authoriz;
	}

	/**
	 * 获取用户拥有权限的项目名
	 * @param array $groupIds
	 * @return array
	 */
	public function getAppNames($groupIds)
	{
		return $this->_groups->getAppNames($groupIds);
	}

	/**
	 * 验证登录名
	 * @param string $loginName
	 * @return array
	 */
	public function checkName($loginName)
	{
		if (($loginName = trim($loginName)) === '') {
			$errNo = DataAccount::ERROR_LOGIN_NAME_EMPTY;
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$row = $this->_users->findByLoginName($loginName);
		if (!$row || !is_array($row) || !isset($row['user_id'], $row['login_name'], $row['password'], $row['salt'], $row['valid_mail'], $row['valid_phone'], $row['trash'], $row['forbidden'], $row['login_count'])) {
			$errNo = DataAccount::ERROR_LOGIN_NAME_NOT_EXISTS;
			Log::warning(sprintf(
				'Account login_name not exists, login_name "%s"', $loginName
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$errNo = DataAccount::SUCCESS_LOGIN_NUM;
		return array(
			'err_no' => $errNo,
			'data' => $row
		);
	}

	/**
	 * 验证登录名和密码
	 * @param string $loginName
	 * @param string $password
	 * @return integer
	 */
	public function checkNamePwd($loginName, $password)
	{
		$data = array();

		if (($loginName = trim($loginName)) === '') {
			$errNo = DataAccount::ERROR_LOGIN_NAME_EMPTY;
			return array(
				'err_no' => $errNo,
				'data' => $data
			);
		}

		if (($password = trim($password)) === '') {
			$errNo = DataAccount::ERROR_PASSWORD_EMPTY;
			return array(
				'err_no' => $errNo,
				'data' => $data
			);
		}

		$ret = $this->checkName($loginName);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			return $ret;
		}

		$data = $ret['data'];
		$password = $this->_users->encrypt($password, $data['salt']);
		if ($password !== $data['password']) {
			$errNo = DataAccount::ERROR_PASSWORD_WRONG;
			Log::warning(sprintf(
				'Account password wrong, user_id "%d", login_name "%s"', $data['user_id'], $data['login_name']
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$errNo = DataAccount::SUCCESS_LOGIN_NUM;
		return array(
			'err_no' => $errNo,
			'data' => $data
		);
	}

	/**
	 * 验证用户登录
	 * @param array $users
	 * @param boolean $update
	 * @return array
	 */
	public function checkLogin(array $users, $update = true)
	{
		$userId       = isset($users['user_id'])       ? (int) $users['user_id']       : 0;
		$loginName    = isset($users['login_name'])    ? $users['login_name']          : '';
		$loginType    = isset($users['login_type'])    ? $users['login_type']          : '';
		$password     = isset($users['password'])      ? $users['password']            : '';
		$salt         = isset($users['salt'])          ? $users['salt']                : '';
		$userName     = isset($users['user_name'])     ? $users['user_name']           : '';
		$userMail     = isset($users['user_mail'])     ? $users['user_mail']           : '';
		$userPhone    = isset($users['user_phone'])    ? $users['user_phone']          : '';
		$dtRegistered = isset($users['dt_registered']) ? $users['dt_registered']       : '';
		$dtLastLogin  = isset($users['dt_last_login']) ? $users['dt_last_login']       : '';
		$dtLastRepwd  = isset($users['dt_last_repwd']) ? $users['dt_last_repwd']       : '';
		$ipRegistered = isset($users['ip_registered']) ? (int) $users['ip_registered'] : 0;
		$ipLastLogin  = isset($users['ip_last_login']) ? (int) $users['ip_last_login'] : 0;
		$ipLastRepwd  = isset($users['ip_last_repwd']) ? (int) $users['ip_last_repwd'] : 0;
		$loginCount   = isset($users['login_count'])   ? (int) $users['login_count']   : 0;
		$repwdCount   = isset($users['repwd_count'])   ? (int) $users['repwd_count']   : 0;
		$groupIds     = isset($users['group_ids'])     ? (array) $users['group_ids']   : array();
		$validMail    = ($users['valid_mail']  === DataUsers::VALID_MAIL_Y)  ? true  : false;
		$validPhone   = ($users['valid_phone'] === DataUsers::VALID_PHONE_Y) ? true  : false;
		$trash        = ($users['trash']       === DataUsers::TRASH_N)       ? false : true;
		$forbidden    = ($users['forbidden']   === DataUsers::FORBIDDEN_N)   ? false : true;

		$data = array(
			'user_id'       => $userId,
			'login_name'    => $loginName,
			'login_type'    => $loginType,
			'password'      => $password,
			'salt'          => $salt,
			'user_name'     => $userName,
			'user_mail'     => $userMail,
			'user_phone'    => $userPhone,
			'dt_registered' => $dtRegistered,
			'dt_last_login' => $dtLastLogin,
			'dt_last_repwd' => $dtLastRepwd,
			'ip_registered' => $ipRegistered,
			'ip_last_login' => $ipLastLogin,
			'ip_last_repwd' => $ipLastRepwd,
			'login_count'   => $loginCount,
			'repwd_count'   => $repwdCount,
			'group_ids'     => $groupIds,
			'valid_mail'    => $validMail,
			'valid_phone'   => $validPhone,
			'trash'         => $trash,
			'forbidden'     => $forbidden
		);

		if ($userId <= 0 || $loginName === '') {
			$errNo = DataAccount::ERROR_LOGIN_FAILED;
			Log::warning(sprintf(
				'Account user_id and login_name must be not empty, user_id "%d", login_name "%s"', $userId, $loginName
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		if ($trash) {
			$errNo = DataAccount::ERROR_USER_TRASH;
			Log::warning(sprintf(
				'Account user has been trashed, user_id "%d", login_name "%s"', $userId, $loginName
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => $data,
			);
		}

		if ($forbidden) {
			$errNo = DataAccount::ERROR_USER_FORBIDDEN;
			Log::warning(sprintf(
				'Account user has been forbidden, user_id "%d", login_name "%s"', $userId, $loginName
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => $data,
			);
		}

		if ($update) {
			$dtLastLogin = date('Y-m-d H:i:s');
			$ipLastLogin = Clean::ip2long(Ap::getRequest()->getClientIp());
			$loginCount += 1;
			$params = array(
				'dt_last_login' => $dtLastLogin,
				'ip_last_login' => $ipLastLogin,
				'login_count' => $loginCount,
			);

			$rowCount = $this->_users->modifyByPk($userId, $params);
			if ($rowCount) {
				$data['dt_last_login'] = $dtLastLogin;
				$data['ip_last_login'] = $ipLastLogin;
				$data['login_count'] = $loginCount;
			}
			else {
				Log::warning(sprintf(
					'Account update dt_last_login|ip_last_login|login_count Failed, user_id "%d", login_name "%s"', $userId, $loginName
				), DataAccount::ERROR_MODIFY_LAST_LOGIN,  __METHOD__);
			}
		}

		$errNo = DataAccount::SUCCESS_LOGIN_NUM;
		return array(
			'err_no' => $errNo,
			'data' => $data
		);
	}

	/**
	 * 将用户身份信息设置到Cookie中
	 * @param array $users
	 * @param boolean $rememberMe
	 * @return array
	 */
	public function setIdentity(array $users, $rememberMe = false)
	{
		$clusterName = self::CLUSTER_NAME;

		$config = Cfg::getApp($clusterName);
		$expiry           = isset($config['expiry'])            ? (int) $config['expiry']                : 0;
		$cookieName       = isset($config['cookie_name'])       ? trim($config['cookie_name'])           : '';
		$cooksetPassword  = isset($config['cookset_password'])  ? (boolean) $config['cookset_password']  : false;
		$cooksetRoleNames = isset($config['cookset_rolenames']) ? (boolean) $config['cookset_rolenames'] : false;
		$cooksetAppNames  = isset($config['cookset_appnames'])  ? (boolean) $config['cookset_appnames']  : false;

		if ($cookieName === '') {
			$errNo = DataAccount::ERROR_LOGIN_FAILED;
			Log::warning(sprintf(
				'Account cookie name must be string and not empty, cluster_name "%s"', $clusterName
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$userId    = isset($users['user_id'])    ? (int) $users['user_id']     : 0;
		$loginName = isset($users['login_name']) ? $users['login_name']        : '';
		$nickname  = isset($users['user_name'])  ? $users['user_name']         : '';
		$password  = isset($users['password'])   ? $users['password']          : '';
		$groupIds  = isset($users['group_ids'])  ? (array) $users['group_ids'] : array();
		$appNames  = $this->getAppNames($groupIds);

		if ($userId <= 0 || $loginName === '') {
			$errNo = DataAccount::ERROR_LOGIN_FAILED;
			Log::warning(sprintf(
				'Account user_id and login_name must be not empty, cluster_name "%s", cookie_name "%s", user_id "%d", login_name "%s"', $clusterName, $cookieName, $userId, $loginName
			), $errNo,  __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$roleNames = array();
		if ($cooksetRoleNames) {
			foreach ($groupIds as $groupId) {
				if (($groupId = (int) $groupId) > 0) {
					$roleNames[] = $groupId;
				}
			}

			$roleNames = array_unique($roleNames);
		}

		$extends = '';
		if ($cooksetAppNames) {
			$extends .= implode(',', $appNames);
		}

		$rememberMe      || $expiry = 0;
		$cooksetPassword || $password = '';

		$authentica = new Authentica($clusterName);
		$ret = $authentica->setIdentity($userId, $loginName, $password, $expiry, $nickname, $roleNames, $extends);
		if ($ret) {
			$errNo = DataAccount::SUCCESS_LOGIN_NUM;
			return array(
				'err_no' => $errNo,
				'data' => $users
			);
		}

		$errNo = DataAccount::ERROR_LOGIN_FAILED;
		Log::warning(sprintf(
			'Account set identity Failed, cluster_name "%s", cookie_name "%s", user_id "%d", login_name "%s"', $clusterName, $cookieName, $userId, $loginName
		), $errNo,  __METHOD__);
		return array(
			'err_no' => $errNo,
			'data' => array()
		);
	}

	/**
	 * 移除Cookie中的用户身份
	 * @return boolean
	 */
	public function clearIdentity()
	{
		$clusterName = self::CLUSTER_NAME;

		$config = Cfg::getApp($clusterName);
		$cookieName = isset($config['cookie_name']) ? trim($config['cookie_name']) : '';

		if ($cookieName === '') {
			$errNo = DataAccount::ERROR_LOGOUT_FAILED;
			Log::warning(sprintf(
				'Account cookie name must be string and not empty, cluster_name "%s"', $clusterName
			), $errNo,  __METHOD__);
			return false;
		}

		$authentica = new Authentica($clusterName);
		$ret = $authentica->clearIdentity();
		if ($ret) {
			return true;
		}

		$errNo = DataAccount::ERROR_LOGOUT_FAILED;
		Log::warning(sprintf(
			'Account clear identity Failed, cluster_name "%s", cookie_name "%s"', $clusterName, $cookieName
		), $errNo,  __METHOD__);
		return false;
	}

	/**
	 * 从Cookie中获取用户身份信息并设置到用户身份管理类
	 * @return boolean
	 */
	public function initIdentity()
	{
		$clusterName = self::CLUSTER_NAME;

		$config = Cfg::getApp($clusterName);
		$expiry           = isset($config['expiry'])            ? (int) $config['expiry']                : 0;
		$cookieName       = isset($config['cookie_name'])       ? trim($config['cookie_name'])           : '';
		$cooksetPassword  = isset($config['cookset_password'])  ? (boolean) $config['cookset_password']  : false;
		$cooksetRoleNames = isset($config['cookset_rolenames']) ? (boolean) $config['cookset_rolenames'] : false;
		$cooksetAppNames  = isset($config['cookset_appnames'])  ? (boolean) $config['cookset_appnames']  : false;

		if ($cookieName === '') {
			Log::warning(sprintf(
				'Account cookie name must be string and not empty, cluster_name "%s"', $clusterName
			), 0,  __METHOD__);

			return false;
		}

		$authentica = new Authentica($clusterName);
		$data = $authentica->getIdentity();
		if (!$data || !is_array($data) || !isset($data['user_id'])) {
			Log::debug(sprintf(
				'Account cookie data must be array and not empty, cluster_name "%s", cookie_name "%s"', $clusterName, $cookieName
			), 0,  __METHOD__);

			return false;
		}

		$userId    = isset($data['user_id'])    ? (int) $data['user_id']      : 0;
		$loginName = isset($data['user_name'])  ? trim($data['user_name'])    : '';
		$password  = isset($data['password'])   ? $data['password']           : '';
		$ip        = isset($data['ip'])         ? (int) $data['ip']           : 0;
		$expiry    = isset($data['expiry'])     ? (int) $data['expiry']       : 0;
		$time      = isset($data['time'])       ? (int) $data['time']         : 0;
		$nickname  = isset($data['nickname'])   ? trim($data['nickname'])     : '';
		$roleNames = isset($data['role_names']) ? (array) $data['role_names'] : array();
		$extends   = isset($data['extends'])    ? $data['extends']            : '';

		if ($userId <= 0 || $loginName === '') {
			Log::warning(sprintf(
				'Account cookie user_id and login_name must be not empty, cluster_name "%s", cookie_name "%s", user_id "%d", login_name "%s"', $clusterName, $cookieName, $userId, $loginName
			), 0,  __METHOD__);

			return false;
		}

		$clientIp = ip2long(Ap::getRequest()->getClientIp());
		if ($ip !== $clientIp) {
			Log::warning(sprintf(
				'Account cookie ip "%s" is not equal to client ip "%s", cluster_name "%s", cookie_name "%s", user_id "%d", login_name "%s"', 
				long2ip($ip), long2ip($clientIp), $clusterName, $cookieName, $userId, $loginName
			), 0,  __METHOD__);

			return false;
		}

		if ($cooksetPassword) {
			if ($password === '') {
				Log::warning(sprintf(
					'Account config cookset_password and cookie password must be not empty, cluster_name "%s", cookie_name "%s", user_id "%d", login_name "%s"', $clusterName, $cookieName, $userId, $loginName
				), 0,  __METHOD__);

				return false;
			}

			$dbpwd = $this->_users->getPasswordByUserId($userId);
			if ($password !== $dbpwd) {
				Log::warning(sprintf(
					'Account cookie password "%s" is not equal to db password "%s", cluster_name "%s", cookie_name "%s", user_id "%d", login_name "%s"', $clusterName, $cookieName, $userId, $loginName
				), 0,  __METHOD__);

				return false;
			}
		}

		$groupIds = $roleNames;
		$appNames = explode(',', $extends);
		$authoriz = $this->getAuthoriz($roleNames);

		Identity::setAll($userId, $loginName, $nickname, $roleNames, $appNames, 0, 0, $authoriz);
		return true;
	}

	/**
	 * 通过登录名和密码登录
	 * @param string $loginName
	 * @param string $password
	 * @param boolean $rememberMe
	 * @return array
	 */
	public function loginByNamePwd($loginName, $password, $rememberMe = false)
	{
		$ret = $this->checkNamePwd($loginName, $password);
		$ret['err_msg'] = DataAccount::getErrMsgByErrNo($ret['err_no']);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			return $ret;
		}

		$ret = $this->checkLogin($ret['data'], true);
		$ret['err_msg'] = DataAccount::getErrMsgByErrNo($ret['err_no']);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			return $ret;
		}

		$ret = $this->setIdentity($ret['data'], $rememberMe);
		$ret['err_msg'] = DataAccount::getErrMsgByErrNo($ret['err_no']);
		return $ret;
	}

	/**
	 * 注销账户
	 * @return boolean
	 */
	public function logout()
	{
		return $this->clearIdentity();
	}
}
