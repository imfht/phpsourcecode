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
use tfc\auth\Identity;

/**
 * Social class file
 * Ajax修改会员详情
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Social.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Social extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$mod = Model::getInstance('Social', 'member');
		$ret = $mod->modifyByPk(Identity::getUserId(), $req->getPost());

		$this->display($ret);
	}
}
