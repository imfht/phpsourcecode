<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\tools;

use library\actions;
use tfc\saf\Text;
use libapp\ErrorNo;
use system\services\Tools;

/**
 * Cacheclear class file
 * 清理缓存
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Cacheclear.php 1 2014-08-19 23:04:36Z huan.song $
 * @package modules.system.action.tools
 * @since 1.0
 */
class Cacheclear extends actions\Remove
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = Text::_('MOD_SYSTEM_SYSTEM_TOOLS_CACHECLEAR_SUCCESS');

		if (!Tools::cacheclear()) {
			$errNo = ErrorNo::ERROR_CACHE_DELETE;
			$errMsg = Text::_('MOD_SYSTEM_SYSTEM_TOOLS_CACHECLEAR_FAILED');
		}

		$data = array(
			'err_no' => $errNo,
			'err_msg' => $errMsg
		);

		$this->render($data);
	}
}
