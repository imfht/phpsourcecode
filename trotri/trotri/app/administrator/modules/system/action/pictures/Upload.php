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

/**
 * Upload class file
 * 上传图片
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Upload.php 1 2014-08-08 15:49:14Z huan.song $
 * @package modules.users.action.users
 * @since 1.0
 */
class Upload extends actions\Create
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$this->render();
	}
}
