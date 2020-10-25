<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\users\action\amcas;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Synch class file
 * 同步控制器
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Synch.php 1 2014-05-29 14:36:52Z Code Generator $
 * @package modules.users.action.amcas
 * @since 1.0
 */
class Synch extends actions\Remove
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$id = Ap::getRequest()->getInteger('id');
		$mod = Model::getInstance('Amcas');
		$mod->synch($id);
	}
}
