<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\action\show;

use library\ShowAction;
use tfc\saf\Cookie;

/**
 * Test class file
 * 测试页
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Test.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.system.action.show
 * @since 1.0
 */
class Test extends ShowAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$cookie = new Cookie('cookie');
		
		
		
		//var_dump($cookie->get('state'));
		
		$httpReferer = $cookie->get('http_referer', 'index.php');
		var_dump($httpReferer);
		
		// 98683de26fdf0b3173de67f3c0d4c9b8
		// 98683de26fdf0b3173de67f3c0d4c9b8
	}
}
