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
 * Reg class file
 * Ajax会员注册
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Reg.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Reg extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$loginName = $req->getTrim('login_name');
		$password = $req->getTrim('password');
		$repassword = $req->getTrim('repassword');

		$mod = Model::getInstance('Account', 'member');
		$ret = $mod->register($loginName, $password, $repassword);

		$this->display($ret);
	}
}
