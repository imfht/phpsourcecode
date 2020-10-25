<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\action\posts;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Batchmodifysort class file
 * 批量编辑排序
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Batchmodifysort.php 1 2014-09-12 17:33:45Z Code Generator $
 * @package modules.posts.action.posts
 * @since 1.0
 */
class Batchmodifysort extends actions\Modify
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$ret = array();

		$req = Ap::getRequest();
		$mod = Model::getInstance('Posts');

		$param = $req->getParam('sort');
		$ret = $mod->batchModifySort($param);

		$url = $this->applyParams($mod->getLLU(), $ret);
		$this->redirect($url);
	}
}
