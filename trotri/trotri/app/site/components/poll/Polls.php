<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\poll;

use libapp\Component;
use tfc\mvc\Mvc;
use components\poll\helpers\Polls AS Helper;

/**
 * Polls class file
 * 投票组件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Polls.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.poll
 * @since 1.0
 */
class Polls extends Component
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$pollKey = isset($this->poll_key) ? trim($this->poll_key) : '';
		if ($pollKey === '') {
			return ;
		}

		$row = Helper::getUsable($pollKey);
		$isShow = false;
		if ($row && is_array($row) && isset($row['options']) && is_array($row['options'])) {
			$isShow = true;
		}

		$this->assign('is_show', $isShow);
		$this->assign($row);
		$this->display();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libapp\Component::getWidgetDirectory()
	 */
	public function getWidgetDirectory()
	{
		list(, $comName, $className) = explode('\\', strtolower(get_class($this)));

		if ($this->_widgetDirectory === null) {
			$viw = Mvc::getView();
			$this->_widgetDirectory = $viw->viewDirectory . DS . $viw->skinName . DS . 'components' . DS . $comName . DS . $this->poll_key;
		}

		return $this->_widgetDirectory;
	}
}
