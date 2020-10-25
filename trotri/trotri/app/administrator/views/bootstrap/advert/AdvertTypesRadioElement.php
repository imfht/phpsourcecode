<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\advert;

use views\bootstrap\components\form;
use tfc\ap\Ap;

/**
 * IRadioElement class file
 * 美化版Radio表单元素，基于Bootstrap-v3前端开发框架的iCheck插件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: AdvertTypesRadioElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.advert
 * @since 1.0
 */
class AdvertTypesRadioElement extends form\IRadioElement
{
	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\IRadioElement::getInput()
	 */
	public function getInput()
	{
		$req = Ap::getRequest();
		$imgUrl = $req->getBaseUrl() . '/static/images/advtypes/';
		$imgExt = '.gif';

		$this->setAttribute('class', 'icheck');

		$type = $this->getType();
		$name = $this->getName(true);
		$attributes = $this->getAttributes();
		$html = $this->getHtml();

		$tagName = 'label';
		$tagAttributes = array('class' => 'checkbox-inline');

		$output = '';
		$p = 0;
		foreach ($this->options as $value => $prompt) {
			if (($p++) % 4 === 0 && $p !== 1) {
				$output .= $html->tag('label', array('class' => 'col-lg-2 control-label'), '');
			}

			$prompt = $html->img($imgUrl . $value . $imgExt, $prompt, array('title' => $prompt));

			$checked = ($value == $this->value) ? true : false;
			$output .= $html->tag($tagName, $tagAttributes, $html->$type($name, $value, $checked, $attributes));
			$output .= $html->tag($tagName, $tagAttributes, $prompt);
			if ($p % 4 === 0) {
				$output .= $html->tag('br');
			}
		}

		return $output;
	}
}
