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
use libapp\Model;

/**
 * Create class file
 * 新增数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Create.php 1 2014-10-22 16:43:30Z Code Generator $
 * @package modules.menus.action.menus
 * @since 1.0
 */
class Create extends actions\Create
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$mod = Model::getInstance('Menus');
		$typeKey = $mod->getTypeKey();
		if ($typeKey === '') {
			$this->err404();
		}

		$this->assign('type_key', $typeKey);
		$this->execute('Menus');
	}
}
