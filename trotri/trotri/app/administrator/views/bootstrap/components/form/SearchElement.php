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
 * InputElement class file
 * 输入框类表单元素，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: InputElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class SearchElement extends form\InputElement
{
	/**
	 * @var string 格式：datetime | date | time
	 */
	protected $_format = 'datetime';

	/**
	 * @var string 表单样式名
	 */
	protected $_className = 'form-control input-sm';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\Element::_init()
	 */
	protected function _init()
	{
		if ($this->_className !== '') {
			$this->setClass($this->_className);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::getInput()
	 */
	public function getInput()
	{
		if ($this->getType() === 'datetimepicker') {
			$this->setAttribute('format', $this->_format);
			$this->setType('text');
			$format = $this->getAttribute('format');
			$this->setClass($this->getClass() . ' form_' . $format);			
		}

		$output = parent::getInput();
		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::fetch()
	 */
	public function fetch()
	{
		return $this->getHr() . $this->getInput();
	}

	/**
	 * 获取分隔线
	 * @return string
	 */
	public function getHr()
	{
		return $this->getHtml()->tag('hr', array('class' => 'hr-condensed'));
	}
}
