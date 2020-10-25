<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\options;

use library\actions;
use tfc\ap\Ap;
use tfc\mvc\Mvc;
use libapp\Model;
use library\SubmitType;
use library\ErrorNo;

/**
 * Modify class file
 * 编辑数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modify.php 1 2014-08-19 23:04:36Z Code Generator $
 * @package modules.system.action.options
 * @since 1.0
 */
class Modify extends actions\Modify
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$ret = array();

		$req = Ap::getRequest();
		$mod = Model::getInstance('Options');
		$submitType = new SubmitType();
		if ($submitType->isPost()) {
			$ret = $mod->batchReplace($req->getPost());
			if ($ret['err_no'] === ErrorNo::SUCCESS_NUM) {
				$this->forward($mod->actNameModify, Mvc::$controller, Mvc::$module, $ret);
			}

			$ret['data'] = $req->getPost();
		}
		else {
			$ret = $mod->findPairs();
		}

		$this->assign('elements', $mod);
		$this->render($ret);
	}
}
