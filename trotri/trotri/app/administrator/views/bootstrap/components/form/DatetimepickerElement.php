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

/**
 * DatetimepickerElement class file
 * Datetimepicker表单元素，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DatetimepickerElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class DatetimepickerElement extends InputElement
{
	/**
	 * @var string 格式：datetime | date | time
	 */
	protected $_format = 'datetime';

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::_init()
	 */
	protected function _init()
	{
		$this->setAttribute('format', $this->_format);
		parent::_init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::getInput()
	 */
	public function getInput()
	{
		$this->setType('text');
		$format = $this->getAttribute('format');
		$this->setClass($this->getClass() . ' form_' . $format);

		$output = parent::getInput();
		return $output;
	}
}
