<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\show;

use library;
use tfc\ap\Ap;
use tfc\saf\Text;
use libapp\Model;
use member\services\DataRepwd;

/**
 * Repwdmail class file
 * 通过邮箱找回密码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Login.php 1 2014-08-08 15:49:14Z huan.song $
 * @package modules.member.action.show
 * @since 1.0
 */
class Repwdmail extends library\ShowAction
{
	/**
	 * @var boolean 是否验证登录
	 */
	protected $_validLogin = false;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		Text::_('MOD_MEMBER__');

		$ciphertext = $req->getTrim('cipher');
		$mod = Model::getInstance('Repwd', 'member');
		$ret = $mod->checkCiphertext($ciphertext);

		if ($ret['err_no'] === DataRepwd::SUCCESS_REPWD_NUM) {
			$this->assign('login_name', $ret['data']['login_name']);
			$this->assign('cipher', $ciphertext);
		}

		$this->render($ret);
	}
}
