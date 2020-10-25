<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\menus\action\menus;

use library\actions;
use tfc\ap\Ap;
use libapp\Model;

/**
 * Index class file
 * 查询数据列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-10-22 16:43:30Z Code Generator $
 * @package modules.menus.action.menus
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
		$typeKey = Ap::getRequest()->getTrim('type_key');
		if ($typeKey === '') {
			$this->err404();
		}

		$ret = array();
		$mod = Model::getInstance('Menus');

		$typeName = $mod->getTypeNameByTypeKey($typeKey);
		$ret = $mod->findLists($typeKey);

		$mod->setLLU(array('type_key' => $typeKey));

		$this->assign('type_key', $typeKey);
		$this->assign('type_name', $typeName);
		$this->assign('elements', $mod);
		$this->render($ret);
	}
}
