<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace components\adverts;

use libapp\Component;
use tfc\mvc\Mvc;
use components\adverts\helpers\Adverts AS Helper;

/**
 * Notice class file
 * 单条广告组件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Notice.php 1 2013-04-20 17:11:06Z huan.song $
 * @package components.adverts
 * @since 1.0
 */
class Advert extends Component
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$typeKey = isset($this->type_key) ? trim($this->type_key) : '';
		if ($typeKey === '') {
			return ;
		}

		$row = Helper::getRow($typeKey);
		$isShow = false;
		if ($row && is_array($row) && isset($row['show_code'])) {
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
			$this->_widgetDirectory = $viw->viewDirectory . DS . $viw->skinName . DS . 'components' . DS . $comName . DS . $this->type_key;
		}

		return $this->_widgetDirectory;
	}
}
