<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\pictures;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Index class file
 * 查询数据列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-09-29 23:33:28Z huan.song $
 * @package modules.system.action.pictures
 * @since 1.0
 */
class Index extends actions\Index
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$data = array();
		$req = Ap::getRequest();
		$mod = Model::getInstance('Pictures');

		$directory = $req->getInteger('directory');
		$funcName = (strlen($directory) > 6) ? 'getFiles' : 'getDirs';
		$data = $mod->$funcName($directory);

		$ret = array(
			'directory' => $directory,
			'data' => $data
		);

		$this->assign('elements', $mod);
		$this->render($ret);
	}

}
