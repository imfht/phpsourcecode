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

use tfc\mvc\Widget;
use tfc\mvc\Mvc;

/**
 * Component class file
 * 页面装饰组件基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Component.php 1 2013-04-05 20:00:06Z huan.song $
 * @package libapp
 * @since 1.0
 */
abstract class Component extends Widget
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::getWidgetDirectory()
	 */
	public function getWidgetDirectory()
	{
		list(, $comName, $className) = explode('\\', strtolower(get_class($this)));

		if ($this->_widgetDirectory === null) {
			$viw = Mvc::getView();
			$this->_widgetDirectory = $viw->viewDirectory . DS . $viw->skinName . DS . 'components' . DS . $comName . DS . $className;
		}

		return $this->_widgetDirectory;
	}
}
