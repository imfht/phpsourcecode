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

/**
 * Index class file
 * 查询数据列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-10-18 13:56:27Z Code Generator $
 * @package modules.posts.action.posts
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
		Ap::getRequest()->setParam('trash', 'n');
		$this->execute('Posts');
	}
}
