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
 * ICheckboxElement class file
 * 美化版Checkbox表单元素，基于Bootstrap-v3前端开发框架的iCheck插件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ICheckboxElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class ICheckboxElement extends InputElement
{
	/**
	 * @var string 表单元素的类型
	 */
	protected $_type = 'checkbox';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::getInput()
	 */
	public function getInput()
	{
		$name = $this->getName(true);
		if (strpos($name, '[') === false) {
			$name .= '[]';
			$this->setName($name);
		}

		$this->setAttribute('class', 'icheck');

		$type = $this->getType();
		$attributes = $this->getAttributes();
		$values = (array) $this->value;
		$html = $this->getHtml();

		$tagName = 'label';
		$tagAttributes = array('class' => 'checkbox-inline');

		$output = '';
		foreach ($this->options as $value => $prompt) {
			$checked = (in_array($value, $values)) ? true : false;
			$output .= $html->tag($tagName, $tagAttributes, $html->$type($name, $value, $checked, $attributes));
			$output .= $html->tag($tagName, $tagAttributes, $prompt);
		}

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::openInput()
	 */
	public function openInput()
	{
		return '';
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::closeInput()
	 */
	public function closeInput()
	{
		return '';
	}
}
