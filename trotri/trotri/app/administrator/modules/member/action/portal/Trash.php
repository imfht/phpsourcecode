<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\portal;

use library\actions;

/**
 * Trash class file
 * 移至回收站和从回收站还原
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Trash.php 1 2014-11-26 21:46:18Z Code Generator $
 * @package modules.member.action.portal
 * @since 1.0
 */
class Trash extends actions\Trash
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$this->execute('Portal');
	}
}
