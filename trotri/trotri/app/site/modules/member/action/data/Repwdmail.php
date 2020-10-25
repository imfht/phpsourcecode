<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\data;

use library\DataAction;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Repwdmail class file
 * Ajax通过邮箱找回密码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Repwdsendmail.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Repwdmail extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$ciphertext = $req->getTrim('cipher');
		$password = $req->getTrim('password');
		$repassword = $req->getTrim('repassword');

		$mod = Model::getInstance('Repwd', 'member');
		$ret = $mod->repwdByCipher($ciphertext, $password, $repassword);

		$this->display($ret);
	}
}
