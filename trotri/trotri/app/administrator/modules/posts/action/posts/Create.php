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
use libapp\Model;

/**
 * Create class file
 * 新增数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Create.php 1 2014-10-18 13:56:27Z Code Generator $
 * @package modules.posts.action.posts
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
		$mod = Model::getInstance('Modules');
		$fields = json_encode($mod->getFields());

		$this->assign('fields', $fields);
		$this->execute('Posts');
	}
}
