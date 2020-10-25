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
use tfc\auth\Identity;
use member\services\Repwd AS SrvRepwd;

/**
 * Repwd class file
 * 会员找回密码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Repwd.php 1 2014-08-08 14:05:27Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Repwd extends BaseModel
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
		$this->_service = new SrvRepwd();
	}

	/**
	 * 通过原始密码修改
	 * @param string $oldPwd
	 * @param string $password
	 * @param string $repassword
	 * @return array
	 */
	public function repwdByOldPwd($oldPwd, $password, $repassword)
	{
		$ret = $this->_service->repwdByOldPwd(Identity::getLoginName(), $oldPwd, $password, $repassword);
		$ret['data'] = array(
			'old_pwd' => $oldPwd,
			'password' => $password,
			'repassword' => $repassword
		);

		return $ret;
	}

	/**
	 * 验证密文是否正确
	 * @param string $ciphertext
	 * @return array
	 */
	public function checkCiphertext($ciphertext)
	{
		$ret = $this->_service->checkCiphertext($ciphertext);
		return $ret;
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
		$ret = $this->_service->repwdByCipher($ciphertext, $password, $repassword);
		$ret['data'] = array(
			'ciphertext' => $ciphertext,
			'password' => $password,
			'repassword' => $repassword
		);

		return $ret;
	}

	/**
	 * 发送邮件
	 * @param string $memberMail
	 * @return array
	 */
	public function sendMail($memberMail)
	{
		$ret = $this->_service->sendMail($memberMail);
		$ret['data'] = array(
			'member_mail' => $memberMail,
		);

		return $ret;
	}
}
