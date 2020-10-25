<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\users;

use views\bootstrap\components\form;

/**
 * UserGroupsCheckboxElement class file
 * 美化版Checkbox表单元素，基于Bootstrap-v3前端开发框架的iCheck插件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: UserGroupsCheckboxElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.users
 * @since 1.0
 */
class UserGroupsCheckboxElement extends form\ICheckboxElement
{
	/**
	 * @var string 表单元素的类型
	 */
	protected $_type = 'checkbox';

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\ICheckboxElement::getInput()
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
			$output .= $html->tag('label', array('class' => 'col-lg-1 control-label'), '');
			$output .= $html->tag($tagName, $tagAttributes, $html->$type($name, $value, $checked, $attributes));
			$output .= $html->tag($tagName, $tagAttributes, $prompt);
			$output .= $html->tag('br');
		}

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::openLabel()
	 */
	public function openLabel()
	{
		return '';
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::closeLabel()
	 */
	public function closeLabel()
	{
		return '';
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::openPrompt()
	 */
	public function openPrompt()
	{
		return '';
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::closePrompt()
	 */
	public function closePrompt()
	{
		return '';
	}
}
