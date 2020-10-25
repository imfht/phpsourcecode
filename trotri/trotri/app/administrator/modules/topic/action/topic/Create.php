<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\topic\action\topic;

use library\actions;

/**
 * Create class file
 * 新增数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Create.php 1 2014-11-04 17:38:30Z Code Generator $
 * @package modules.topic.action.topic
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
		$this->execute('Topic');
	}
}
