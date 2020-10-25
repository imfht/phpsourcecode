<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\site;

use library\actions;
use system\services\Options;

/**
 * Index class file
 * 首页
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Index.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.system.action.site
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
		$install = DIR_ROOT . DS . 'webroot' . DS . 'install.php';
		$this->assign('install', $install);

		$sysInfo = Options::getSysInfo();
		$devInfo = Options::getDevInfo();

		$this->assign('sys_info', $sysInfo);
		$this->assign('dev_info', $devInfo);
		$this->render();
	}
}
