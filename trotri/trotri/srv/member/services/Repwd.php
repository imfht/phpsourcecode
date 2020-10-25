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

use tfc\util\String;
use tfc\validator\MailValidator;
use tfc\saf\Mef;
use tfc\saf\Log;
use users\library\Lang;
use system\services\Tools;
use system\services\Options;

/**
 * Repwd class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Repwd.php 1 2014-08-07 10:09:58Z huan.song $
 * @package member.services
 * @since 1.0
 */
class Repwd
{
	/**
	 * @var string Key配置名
	 */
	const CLUSTER_NAME = 'repwd';

	/**
	 * @var integer 邮箱链接有效期，单位：分钟，最长不超过1天
	 */
	const MAIL_LINK_EXPIRY = 120;

	/**
	 * @var instance of member\services\Portal
	 */
	protected $_portal = null;

	/**
	 * @var instance of tfc\saf\Mef
	 */
	protected $_mef = null;

	/**
	 * 构造方法：初始化加密算法管理类、业务处理类
	 */
	public function __construct()
	{
		$this->_mef = Mef::getInstance(self::CLUSTER_NAME);
		$this->_portal = new Portal();
	}

	/**
	 * 通过原始密码修改
	 * @param string $loginName
	 * @param string $oldPwd
	 * @param string $password
	 * @param string $repassword
	 * @return array
	 */
	public function repwdByOldPwd($loginName, $oldPwd, $password, $repassword)
	{
		if (($loginName = trim($loginName)) === '') {
			$errNo = DataRepwd::ERROR_REPWD_FAILED;
			Log::warning('Repwd old password empty', $errNo, __METHOD__);

			return array(
				'err_no' => $errNo,
				'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
			);
		}

		if (($oldPwd = trim($oldPwd)) === '') {
			$errNo = DataRepwd::ERROR_OLD_PASSWORD_EMPTY;
			Log::warning('Repwd old password empty', $errNo, __METHOD__);

			return array(
				'err_no' => $errNo,
				'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
			);
		}

		$row = $this->findByLoginName($loginName);
		if (!$row) {
			$errNo = DataRepwd::ERROR_REPWD_FAILED;
			Log::warning(sprintf(
				'Repwd login_name not exists, login_name "%s"', $loginName
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
			);
		}

		$oldPwd = $this->_portal->encrypt($oldPwd, $row['salt']);
		if ($oldPwd !== $row['password']) {
			$errNo = DataRepwd::ERROR_OLD_PASSWORD_WRONG;
			Log::warning(sprintf(
				'Repwd old password wrong, login_name "%s"', $loginName
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
			);
		}

		return $this->modifyPasswordByPk($row['member_id'], $password, $repassword);
	}

	/**
	 * 通过密文修改
	 * @param string $ciphertext
	 * @param string $password
	 * @param string $repassword
	 * @return array
	 */
	public function repwdByCipher($ciphertext, $password, $repassword)
	{
		$ret = $this->checkCiphertext($ciphertext);
		if ($ret['err_no'] !== DataRepwd::SUCCESS_REPWD_NUM) {
			unset($ret['data']);
			return $ret;
		}

		$data = $ret['data'];
		return $this->modifyPasswordByPk($data['member_id'], $password, $repassword);
	}

	/**
	 * 验证密文是否正确
	 * @param string $ciphertext
	 * @return array
	 */
	public function checkCiphertext($ciphertext)
	{
		$ret = $this->decryptMail($ciphertext);
		if ($ret['err_no'] !== DataRepwd::SUCCESS_REPWD_NUM) {
			$ret['err_msg'] = DataRepwd::getErrMsgByErrNo($ret['err_no']);
			$ret['data'] = array();
			return $ret;
		}

		$data = $ret['data'];
		$row = $this->findByLoginName($data['login_name']);
		if (!$row || $data['password'] !== $this->encryptPwd($row['password']) || $data['dt_last_login'] !== $row['dt_last_login']) {
			$errNo = DataRepwd::ERROR_CIPHERTEXT_WRONG;
			Log::warning(sprintf(
				'Repwd ciphertext wrong or login_name not exists, login_name "%s", password "%s | %s", dt_last_login "%s | %s"',
				$data['login_name'], $data['password'], $this->encryptPwd($row['password']), $data['dt_last_login'], $row['dt_last_login']
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'data' => array(),
				'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
			);
		}

		$data['member_id'] = $row['member_id'];
		$errNo = DataRepwd::SUCCESS_REPWD_NUM;
		return array(
			'err_no' => $errNo,
			'data' => $data,
			'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
		);
	}

	/**
	 * 发送邮件
	 * @param string $memberMail
	 * @return integer
	 */
	public function sendMail($memberMail)
	{
		$ret = $this->encryptMail($memberMail);
		if ($ret['err_no'] !== DataRepwd::SUCCESS_REPWD_NUM) {
			$ret['err_msg'] = DataRepwd::getErrMsgByErrNo($ret['err_no']);
			unset($ret['ciphertext']);
			return $ret;
		}

		$url = Options::getSiteUrl() . '?r=member/show/repwdmail&cipher=' . $ret['ciphertext'];

		$subject = Lang::_('SRV_FILTER_REPWD_REPWD_LABEL');
		$body = $subject . ': ' . $url;
		if (!Tools::sendMail($memberMail, $subject, $body)) {
			$errNo = DataRepwd::ERROR_SEND_MAIL_FAILED;
			Log::warning(sprintf(
				'Repwd send mail failed, member_mail "%s"', $memberMail
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
			);
		}

		$mailHost = substr($memberMail, strpos($memberMail, '@') + 1);

		$errNo = DataRepwd::SUCCESS_REPWD_NUM;
		$errMsg = Lang::_('SRV_FILTER_REPWD_SEND_MAIL_SUCCESS') . '&nbsp;&nbsp;' . '<a href="http://' . $mailHost . '">' . $mailHost . '</a>';
		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
		);
	}

	/**
	 * 通过密文获取邮箱
	 * @param string $ciphertext
	 * @return array
	 */
	public function decryptMail($ciphertext)
	{
		if (($ciphertext = trim($ciphertext)) === '') {
			$errNo = DataRepwd::ERROR_CIPHERTEXT_EMPTY;
			Log::warning('Repwd ciphertext empty', $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'data' => array(),
			);
		}

		$ciphertext = str_replace(' ', '+', $ciphertext);
		$plaintext = $this->_mef->decode($ciphertext);
		if ($plaintext === '' || substr_count($plaintext, '|') !== 4) {
			$errNo = DataRepwd::ERROR_CIPHERTEXT_DECRYPT_FAILED;
			Log::warning(sprintf(
				'Repwd ciphertext decrypt failed, ciphertext "%s"', $ciphertext
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'data' => array(),
			);
		}

		$string = substr($plaintext, 0, strrpos($plaintext, '|'));
		$ascii = (int) substr($plaintext, strrpos($plaintext, '|') + 1);
		if ($ascii !== String::ascii($string)) {
			$errNo = DataRepwd::ERROR_CIPHERTEXT_WRONG;
			Log::warning(sprintf(
				'Repwd ciphertext wrong, ciphertext "%s", ascii "%d"', $ciphertext, $ascii
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'data' => array(),
			);
		}

		list($loginName, $password, $dtLastLogin, $tsCreated) = explode('|', $plaintext);
		$data = array(
			'login_name' => trim($loginName),
			'password' => trim($password),
			'dt_last_login' => trim($dtLastLogin),
			'ts_created' => (int) $tsCreated
		);

		if ((time() - $data['ts_created']) > 60 * self::MAIL_LINK_EXPIRY) {
			$errNo = DataRepwd::ERROR_CIPHERTEXT_TIME_OUT;
			Log::warning(sprintf(
				'Repwd ciphertext time out, ciphertext "%s", ts_created "%s %d", expiry "%d"',
				$ciphertext, date('Y-m-d H:i:s', $data['ts_created']), $data['ts_created'], self::MAIL_LINK_EXPIRY
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'data' => $data,
			);
		}

		$errNo = DataRepwd::SUCCESS_REPWD_NUM;
		return array(
			'err_no' => $errNo,
			'data' => $data,
		);
	}

	/**
	 * 获取加密后的邮箱
	 * @param string $mail
	 * @return string
	 */
	public function encryptMail($mail)
	{
		if (($mail = trim($mail)) === '') {
			$errNo = DataRepwd::ERROR_MEMBER_MAIL_EMPTY;
			Log::warning('Repwd member_mail empty', $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'ciphertext' => '',
			);
		}

		if (!preg_match(MailValidator::REGEX_MAIL, $mail)) {
			$errNo = DataRepwd::ERROR_MEMBER_MAIL_WRONG;
			Log::warning(sprintf(
				'Repwd member_mail wrong, member_mail "%s"', $mail
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'ciphertext' => '',
			);
		}

		$row = $this->_portal->findByLoginName($mail);
		if (!$row) {
			$errNo = DataRepwd::ERROR_MEMBER_MAIL_NOT_EXISTS;
			Log::warning(sprintf(
				'Repwd member_mail not exists, member_mail "%s"', $mail
			), $errNo,  __METHOD__);

			return array(
				'err_no' => $errNo,
				'ciphertext' => '',
			);
		}

		$password = $this->encryptPwd($row['password']);
		$plaintext = $row['login_name'] . '|' . $password . '|' . $row['dt_last_login'] . '|' . time();
		$plaintext .= '|' . String::ascii($plaintext);
		$ciphertext = $this->_mef->encode($plaintext);

		$errNo = DataRepwd::SUCCESS_REPWD_NUM;
		return array(
			'err_no' => $errNo,
			'ciphertext' => $ciphertext,
		);
	}

	/**
	 * 获取加密后的密码
	 * @param string $password
	 * @return string
	 */
	public function encryptPwd($password)
	{
		return substr(md5(strrev(substr($password, 0, 15))), 0, 16);
	}

	/**
	 * 修改密码
	 * @param integer $memberId
	 * @param string $password
	 * @param string $repassword
	 * @return integer
	 */
	public function modifyPasswordByPk($memberId, $password, $repassword)
	{
		$rowCount = $this->_portal->modifyPasswordByPk($memberId, $password, $repassword);
		if ($rowCount === false || $rowCount <= 0) {
			$errNo = DataRepwd::ERROR_REPWD_FAILED;
			Log::warning(sprintf(
				'Repwd modify password failed, member_id "%d", password "%s", repassword "%s"', $memberId, $password, $repassword
			), $errNo,  __METHOD__);

			$errors = $this->_portal->getErrors();
			return array(
				'err_no' => $errNo,
				'err_msg' => array_shift($errors),
			);
		}

		$errNo = DataRepwd::SUCCESS_REPWD_NUM;
		return array(
			'err_no' => $errNo,
			'err_msg' => DataRepwd::getErrMsgByErrNo($errNo),
		);
	}

	/**
	 * 通过登录名，查询一条记录
	 * @param string $loginName
	 * @return array
	 */
	public function findByLoginName($loginName)
	{
		$row = $this->_portal->findByLoginName($loginName);
		if ($row && is_array($row) && isset($row['member_id']) && isset($row['forbidden']) && isset($row['trash'])) {
			if ($row['forbidden'] === DataPortal::FORBIDDEN_N && $row['trash'] === DataPortal::TRASH_N) {
				$memberId    = isset($row['member_id'])     ? (int) $row['member_id'] : 0;
				$loginName   = isset($row['login_name'])    ? $row['login_name']      : '';
				$password    = isset($row['password'])      ? $row['password']        : '';
				$salt        = isset($row['salt'])          ? $row['salt']            : '';
				$dtLastLogin = isset($row['dt_last_login']) ? $row['dt_last_login']   : '';
				if ($memberId > 0 && $loginName !== '' && strlen($password) > 16 && $dtLastLogin !== '' && $salt !== '') {
					return array(
						'member_id' => $memberId,
						'login_name' => $loginName,
						'password' => $password,
						'salt' => $salt,
						'dt_last_login' => $dtLastLogin
					);
				}
			}
		}

		return false;
	}
}
