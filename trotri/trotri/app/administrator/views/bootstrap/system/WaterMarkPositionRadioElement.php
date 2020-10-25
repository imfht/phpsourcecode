<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\system;

use views\bootstrap\components\form;

/**
 * WaterMarkPositionRadioElement class file
 * 美化版Checkbox表单元素，基于Bootstrap-v3前端开发框架的iCheck插件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: WaterMarkPositionRadioElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.system
 * @since 1.0
 */
class WaterMarkPositionRadioElement extends form\IRadioElement
{
	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\IRadioElement::getInput()
	 */
	public function getInput()
	{
		$this->setAttribute('class', 'icheck');

		$type = $this->getType();
		$name = $this->getName(true);
		$attributes = $this->getAttributes();
		$html = $this->getHtml();

		$tagName = 'label';
		$tagAttributes = array('class' => 'checkbox-inline');

		$output = '';
		foreach ($this->options as $value => $prompt) {
			if (($value - 1) % 3 === 0 && $value !== 1) {
				$output .= $html->tag('label', array('class' => 'col-lg-2 control-label'), '');
			}

			$checked = ($value == $this->value) ? true : false;
			$output .= $html->tag($tagName, $tagAttributes, $html->$type($name, $value, $checked, $attributes));
			$output .= $html->tag($tagName, $tagAttributes, $prompt);
			if ($value % 3 === 0) {
				$output .= $html->tag('br');
			}
		}

		return $output;
	}

}
