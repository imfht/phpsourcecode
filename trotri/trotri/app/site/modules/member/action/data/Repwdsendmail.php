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
 * Repwdsendmail class file
 * Ajax发送找回密码邮件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Repwdsendmail.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Repwdsendmail extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$memberMail = $req->getTrim('member_mail');

		$mod = Model::getInstance('Repwd', 'member');
		$ret = $mod->sendMail($memberMail);

		$this->display($ret);
	}
}
