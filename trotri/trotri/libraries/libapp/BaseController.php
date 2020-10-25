<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libapp;

use tfc\ap\ErrorException;
use tfc\mvc\Mvc;
use tfc\mvc\Controller;

/**
 * BaseController abstract class file
 * 控制器基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: BaseController.php 1 2013-04-05 01:08:06Z huan.song $
 * @package libapp
 * @since 1.0
 */
abstract class BaseController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Controller::getActionByMap()
	 */
	public function getActionByMap(array $maps, $id)
	{
		try {
			$action = parent::getActionByMap($maps, $id);
		}
		catch (ErrorException $e) {
			$action = $this->getActionById($id);
		}

		return $action;
	}

	/**
	 * 根据ActionId，获取Action类的路径
	 * @param string $id
	 * @return string
	 */
	public function getActionById($id)
	{
		return 'modules\\' . Mvc::$module . '\\action\\' . Mvc::$controller . '\\' . $id;
	}
}
