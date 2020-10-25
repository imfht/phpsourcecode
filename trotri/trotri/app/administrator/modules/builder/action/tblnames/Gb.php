<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\action\tblnames;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Gb class file
 * 通过表Metadata生成Builders数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Remove.php 1 2014-05-26 19:25:19Z Code Generator $
 * @package modules.builder.action.tblnames
 * @since 1.0
 */
class Gb extends actions\Remove
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$tblName = Ap::getRequest()->getTrim('tbl_name');
		if ($tblName === '') {
			$this->err404();
		}

		$mod = Model::getInstance('Tblnames');
		$mod->gb($tblName);
	}
}
