<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\components\bar;

use tfc\mvc\Widget;
use tfc\ap\Registry;

/**
 * AlertBar class file
 * 页面警告栏
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: AlertBar.php 1 2013-04-20 17:11:06Z huan.song $
 * @package views.bootstrap.components.bar
 * @since 1.0
 */
class AlertBar extends Widget
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$errNo = $this->getView()->err_no;
		$errMsg = $this->getView()->err_msg;

		if ($errMsg != '') {
			$attributes = array(
				'id' => $this->getId(),
				'class' => 'alert alert-' . ($errNo > 0 ? 'danger' : 'success')
			);

			echo $this->getHtml()->tag('div', $attributes, $errMsg);
		}
	}

	/**
	 * 获取警告栏ID
	 * @return string
	 */
	public function getId()
	{
		$id = (int) Registry::get('AlertBar_ID') + 1;
		Registry::set('AlertBar_ID', $id);

		return 'alert_bar_' . $id;
	}
}
