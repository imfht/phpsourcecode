<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\components\form;

use tfc\mvc\form;

/**
 * HiddenElement class file
 * Hidden表单元素，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: HiddenElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class HiddenElement extends form\InputElement
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::fetch()
	 */
	public function fetch()
	{
		return $this->getInput();
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::getInput()
	 */
	public function getInput()
	{
		return $this->getHtml()->hidden($this->getName(true), $this->value, $this->getAttributes());
	}
}
