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

use tfc\ap\Ap;
use tfc\util\String;
use tfc\saf\Cfg;
use tfc\saf\Log;
use tfc\auth\Authentica;
use tfc\auth\Identity;
use libsrv\Clean;
use member\services\Portal;

/**
 * Account class file
 * 业务层：业务处理类
 * <pre>
 * 配置 /cfg/app/appname/main.php：
 * return array (
 *   'account' => array (
 *     'key_name' => 'auth_site',         // 密钥配置名
 *     'domain' => '',                    // Cookie的有效域名，缺省：当前域名
 *     'path' => '/',                     // Cookie的有效服务器路径，缺省：/
 *     'secure' => false,                 // FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输，缺省：FALSE
 *     'httponly' => true,                // TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击，缺省：TRUE
 *     'expiry' => MONTH_IN_SECONDS,      // 记住密码时间
 *     'cookie_name' => 'ptid',           // Cookie名
 *     'cookset_password' => false,       // Cookie中设置密码
 *     'cookset_rolenames' => false,      // Cookie中设置用户拥有的角色名，管理员才有角色，会员没有角色
 *     'cookset_appnames' => false,       // Cookie中设置用户拥有权限的项目名
 *   ),
 * )
 *
 * 配置 /cfg/key/cluster.php：
 * return array (
 *   'auth_site' => array (
 *     'crypt' => 'UViRN53uj7yZ5IAfdIGiq5bvRuCH9njd', // 加密密钥
 *     'sign' => 'xwFVMiM98nzW6PwW9jxCmT2mLTv5IJES',  // 签名密钥
 *     'expiry' => MONTH_IN_SECONDS,                  // 缺省的密文有效期，如果等于0，表示永久有效，单位：秒
 *     'rnd_len' => 20                                // 随机密钥长度，取值 0-32
 *   ),
 * )
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Account.php 1 2014-08-07 10:09:58Z huan.song $
 * @package member.services
 * @since 1.0
 */
class Account
{
	/**
	 * @var string Cookie配置名
	 */
	const CLUSTER_NAME = 'account';

	/**
	 * @var instance of member\services\Portal
	 */
	protected $_portal = null;

	/**
	 * 构造方法：初始化业务处理类
	 */
	public function __construct()
	{
		$this->_portal = new Portal();
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

		$row = $this->_portal->findByLoginName($loginName);
		if (!$row || !is_array($row) || !isset($row['member_id'], $row['login_name'], $row['password'], $row['salt'], $row['valid_mail'], $row['valid_phone'], $row['trash'], $row['forbidden'], $row['login_count'])) {
			$errNo = DataAccount::ERROR_LOGIN_NAME_NOT_EXISTS;
			Log::warning(sprintf(
				'Account login_name not exists, login_name "%s"', $loginName
			), $errNo, __METHOD__);
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
		$password = $this->_portal->encrypt($password, $data['salt']);
		if ($password !== $data['password']) {
			$errNo = DataAccount::ERROR_PASSWORD_WRONG;
			Log::warning(sprintf(
				'Account password wrong, member_id "%d", login_name "%s"', $data['member_id'], $data['login_name']
			), $errNo, __METHOD__);
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
	 * @param array $member
	 * @param boolean $update
	 * @return array
	 */
	public function checkLogin(array $member, $update = true)
	{
		$memberId     = isset($member['member_id'])     ? (int) $member['member_id']     : 0;
		$loginName    = isset($member['login_name'])    ? $member['login_name']          : '';
		$loginType    = isset($member['login_type'])    ? $member['login_type']          : '';
		$password     = isset($member['password'])      ? $member['password']            : '';
		$salt         = isset($member['salt'])          ? $member['salt']                : '';
		$memberName   = isset($member['member_name'])   ? $member['member_name']         : '';
		$memberMail   = isset($member['member_mail'])   ? $member['member_mail']         : '';
		$memberPhone  = isset($member['member_phone'])  ? $member['member_phone']        : '';
		$dtRegistered = isset($member['dt_registered']) ? $member['dt_registered']       : '';
		$dtLastLogin  = isset($member['dt_last_login']) ? $member['dt_last_login']       : '';
		$dtLastRepwd  = isset($member['dt_last_repwd']) ? $member['dt_last_repwd']       : '';
		$ipRegistered = isset($member['ip_registered']) ? (int) $member['ip_registered'] : 0;
		$ipLastLogin  = isset($member['ip_last_login']) ? (int) $member['ip_last_login'] : 0;
		$ipLastRepwd  = isset($member['ip_last_repwd']) ? (int) $member['ip_last_repwd'] : 0;
		$loginCount   = isset($member['login_count'])   ? (int) $member['login_count']   : 0;
		$repwdCount   = isset($member['repwd_count'])   ? (int) $member['repwd_count']   : 0;
		$typeId       = isset($member['type_id'])       ? (int) $member['type_id']       : 0;
		$rankId       = isset($member['rank_id'])       ? (int) $member['rank_id']       : 0;
		$validMail    = ($member['valid_mail']  === DataPortal::VALID_MAIL_Y)  ? true  : false;
		$validPhone   = ($member['valid_phone'] === DataPortal::VALID_PHONE_Y) ? true  : false;
		$trash        = ($member['trash']       === DataPortal::TRASH_N)       ? false : true;
		$forbidden    = ($member['forbidden']   === DataPortal::FORBIDDEN_N)   ? false : true;

		$data = array(
			'member_id' => $memberId,
			'login_name' => $loginName,
			'login_type' => $loginType,
			'password' => $password,
			'salt' => $salt,
			'member_name' => $memberName,
			'member_mail' => $memberMail,
			'member_phone' => $memberPhone,
			'dt_registered' => $dtRegistered,
			'dt_last_login' => $dtLastLogin,
			'dt_last_repwd' => $dtLastRepwd,
			'ip_registered' => $ipRegistered,
			'ip_last_login' => $ipLastLogin,
			'ip_last_repwd' => $ipLastRepwd,
			'login_count' => $loginCount,
			'repwd_count' => $repwdCount,
			'type_id' => $typeId,
			'rank_id' => $rankId,
			'valid_mail' => $validMail,
			'valid_phone' => $validPhone,
			'trash' => $trash,
			'forbidden' => $forbidden,
		);

		if ($memberId <= 0 || $loginName === '') {
			$errNo = DataAccount::ERROR_LOGIN_FAILED;
			Log::warning(sprintf(
				'Account member_id and login_name must be not empty, member_id "%d", login_name "%s"', $memberId, $loginName
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		if ($trash) {
			$errNo = DataAccount::ERROR_MEMBER_TRASH;
			Log::warning(sprintf(
				'Account member has been trashed, member_id "%d", login_name "%s"', $memberId, $loginName
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => $data,
			);
		}

		if ($forbidden) {
			$errNo = DataAccount::ERROR_MEMBER_FORBIDDEN;
			Log::warning(sprintf(
				'Account member has been forbidden, member_id "%d", login_name "%s"', $memberId, $loginName
			), $errNo, __METHOD__);
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

			$rowCount = $this->_portal->modifyByPk($memberId, $params);
			if ($rowCount) {
				$data['dt_last_login'] = $dtLastLogin;
				$data['ip_last_login'] = $ipLastLogin;
				$data['login_count'] = $loginCount;
			}
			else {
				Log::warning(sprintf(
					'Account update dt_last_login|ip_last_login|login_count Failed, member_id "%d", login_name "%s"', $memberId, $loginName
				), DataAccount::ERROR_MODIFY_LAST_LOGIN, __METHOD__);
			}
		}

		$errNo = DataAccount::SUCCESS_LOGIN_NUM;
		return array(
			'err_no' => $errNo,
			'data' => $data
		);
	}

	/**
	 * 掩饰会员名中的手机号码
	 * @param string $memberName
	 * @return string
	 */
	public function maskName($memberName)
	{
		static $phoneRegex = '/^1\d{10}$/';

		if (preg_match($phoneRegex, $memberName)) {
			$memberName = substr_replace($memberName, '******', 3, 6);
		}

		return $memberName;
	}

	/**
	 * 将会员身份信息设置到Cookie中
	 * @param array $member
	 * @param boolean $rememberMe
	 * @return array
	 */
	public function setIdentity(array $member, $rememberMe = false)
	{
		$clusterName = self::CLUSTER_NAME;

		$config = Cfg::getApp($clusterName);
		$expiry           = isset($config['expiry'])            ? (int) $config['expiry']                : 0;
		$cookieName       = isset($config['cookie_name'])       ? trim($config['cookie_name'])           : '';
		$cooksetPassword  = isset($config['cookset_password'])  ? (boolean) $config['cookset_password']  : false;

		if ($cookieName === '') {
			$errNo = DataAccount::ERROR_LOGIN_FAILED;
			Log::warning(sprintf(
				'Account cookie name must be string and not empty, cluster_name "%s"', $clusterName
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$memberId   = isset($member['member_id'])   ? (int) $member['member_id'] : 0;
		$loginName  = isset($member['login_name'])  ? $member['login_name']      : '';
		$memberName = isset($member['member_name']) ? $member['member_name']     : '';
		$password   = isset($member['password'])    ? $member['password']        : '';
		$typeId     = isset($member['type_id'])     ? (int) $member['type_id']   : 0;
		$rankId     = isset($member['rank_id'])     ? (int) $member['rank_id']   : 0;

		if ($memberId <= 0 || $loginName === '') {
			$errNo = DataAccount::ERROR_LOGIN_FAILED;
			Log::warning(sprintf(
				'Account member_id and login_name must be not empty, cluster_name "%s", cookie_name "%s", member_id "%d", login_name "%s"', $clusterName, $cookieName, $memberId, $loginName
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'data' => array()
			);
		}

		$nickname = $this->maskName($memberName);
		$roleNames = array('type_id:' . $typeId, 'rank_id:' . $rankId);
		$extends = '';

		$rememberMe      || $expiry = 0;
		$cooksetPassword || $password = '';

		$authentica = new Authentica($clusterName);
		$ret = $authentica->setIdentity($memberId, $loginName, $password, $expiry, $nickname, $roleNames, $extends);
		if ($ret) {
			$errNo = DataAccount::SUCCESS_LOGIN_NUM;
			return array(
				'err_no' => $errNo,
				'data' => $member
			);
		}

		$errNo = DataAccount::ERROR_LOGIN_FAILED;
		Log::warning(sprintf(
			'Account set identity Failed, cluster_name "%s", cookie_name "%s", member_id "%d", login_name "%s"', $clusterName, $cookieName, $memberId, $loginName
		), $errNo, __METHOD__);
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
			), $errNo, __METHOD__);
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
		), $errNo, __METHOD__);
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

		if ($cookieName === '') {
			Log::warning(sprintf(
				'Account cookie name must be string and not empty, cluster_name "%s"', $clusterName
			), 0, __METHOD__);

			return false;
		}

		$authentica = new Authentica($clusterName);
		$data = $authentica->getIdentity();
		if (!$data || !is_array($data) || !isset($data['user_id'])) {
			Log::debug(sprintf(
				'Account cookie data must be array and not empty, cluster_name "%s", cookie_name "%s"', $clusterName, $cookieName
			), 0, __METHOD__);

			return false;
		}

		$memberId  = isset($data['user_id'])    ? (int) $data['user_id']      : 0;
		$loginName = isset($data['user_name'])  ? trim($data['user_name'])    : '';
		$password  = isset($data['password'])   ? $data['password']           : '';
		$ip        = isset($data['ip'])         ? (int) $data['ip']           : 0;
		$expiry    = isset($data['expiry'])     ? (int) $data['expiry']       : 0;
		$time      = isset($data['time'])       ? (int) $data['time']         : 0;
		$nickname  = isset($data['nickname'])   ? trim($data['nickname'])     : '';
		$roleNames = isset($data['role_names']) ? (array) $data['role_names'] : array();
		$extends   = isset($data['extends'])    ? $data['extends']            : '';

		if ($memberId <= 0 || $loginName === '') {
			Log::warning(sprintf(
				'Account cookie member_id and login_name must be not empty, cluster_name "%s", cookie_name "%s", member_id "%d", login_name "%s"', $clusterName, $cookieName, $memberId, $loginName
			), 0, __METHOD__);

			return false;
		}

		$clientIp = ip2long(Ap::getRequest()->getClientIp());
		if ($ip !== $clientIp) {
			Log::warning(sprintf(
				'Account cookie ip "%s" is not equal to client ip "%s", cluster_name "%s", cookie_name "%s", member_id "%d", login_name "%s"', 
				long2ip($ip), long2ip($clientIp), $clusterName, $cookieName, $memberId, $loginName
			), 0, __METHOD__);

			return false;
		}

		if ($cooksetPassword) {
			if ($password === '') {
				Log::warning(sprintf(
					'Account config cookset_password and cookie password must be not empty, cluster_name "%s", cookie_name "%s", member_id "%d", login_name "%s"', $clusterName, $cookieName, $memberId, $loginName
				), 0, __METHOD__);

				return false;
			}

			$dbpwd = $this->_portal->getPasswordByUserId($memberId);
			if ($password !== $dbpwd) {
				Log::warning(sprintf(
					'Account cookie password "%s" is not equal to db password "%s", cluster_name "%s", cookie_name "%s", member_id "%d", login_name "%s"', $clusterName, $cookieName, $memberId, $loginName
				), 0, __METHOD__);

				return false;
			}
		}

		$typeId = $rankId = 0;
		foreach ($roleNames as $name) {
			$prev = substr($name, 0, 7);
			if ($prev === 'type_id') {
				$typeId = (int) substr($name, 8);
				continue;
			}

			if ($prev === 'rank_id') {
				$rankId = (int) substr($name, 8);
				continue;
			}
		}

		$appNames = array();
		$authoriz = null;

		Identity::setAll($memberId, $loginName, $nickname, $roleNames, $appNames, $typeId, $rankId, $authoriz);
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
	 * 第三方账号登录
	 * @param string $partner
	 * @param string $openid
	 * @return array
	 */
	public function loginByPartner($partner, $openid)
	{
		if (($partner = trim($partner)) === '') {
			$errNo = DataAccount::ERROR_PARTNER_EMPTY;
			return array(
				'err_no' => $errNo,
				'err_msg' => DataAccount::getErrMsgByErrNo($errNo),
				'data' => array()
			);
		}

		if (($openid = trim($openid)) === '') {
			$errNo = DataAccount::ERROR_OPENID_EMPTY;
			return array(
				'err_no' => $errNo,
				'err_msg' => DataAccount::getErrMsgByErrNo($errNo),
				'data' => array()
			);
		}

		if (!in_array($partner, DataAccount::$partners)) {
			$errNo = DataAccount::ERROR_PARTNER_WRONG;
			return array(
				'err_no' => $errNo,
				'err_msg' => DataAccount::getErrMsgByErrNo($errNo),
				'data' => array()
			);
		}

		$loginName = $partner . '_' . $openid;
		$row = $this->_portal->findByLoginName($loginName);
		if (!$row || !is_array($row) || !isset($row['member_id'])) {
			$salt = $this->_portal->getSalt();
			$password = $this->_portal->encrypt(String::randStr(12), $salt);
			$params = array(
				'login_name' => $loginName,
				'login_type' => DataPortal::LOGIN_TYPE_PARTNER,
				'password' => $password,
				'salt' => $salt,
				'member_name' => mt_rand(100000000, 999999999),
				'ip_registered' => Clean::ip2long(Ap::getRequest()->getClientIp())
			);

			if (!$this->_portal->getDb()->create($params)) {
				Log::warning(sprintf(
					'Account db create failed, login_name "%s", login_type "%s"', $loginName, DataPortal::LOGIN_TYPE_PARTNER
				), 0, __METHOD__);
			}
		}

		$ret = $this->checkName($loginName);
		$ret['err_msg'] = DataAccount::getErrMsgByErrNo($ret['err_no']);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			return $ret;
		}

		$ret = $this->checkLogin($ret['data'], true);
		$ret['err_msg'] = DataAccount::getErrMsgByErrNo($ret['err_no']);
		if ($ret['err_no'] !== DataAccount::SUCCESS_LOGIN_NUM) {
			return $ret;
		}

		$ret = $this->setIdentity($ret['data'], false);
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

	/**
	 * 获取业务处理类
	 * @return \member\services\Portal
	 */
	public function getPortal()
	{
		return $this->_portal;
	}

}
