<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\poll\action\data;

use library\DataAction;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Vote class file
 * Ajax提交投票
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Vote.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.poll.action.data
 * @since 1.0
 */
class Vote extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$pollKey = $req->getTrim('key');
		$value = $req->getTrim('value');

		$mod = Model::getInstance('Vote', 'poll');
		$ret = $mod->addVote($pollKey, $value);

		$this->display($ret);
	}
}
