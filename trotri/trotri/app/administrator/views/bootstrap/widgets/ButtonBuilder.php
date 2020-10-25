<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\widgets;

use tfc\mvc\Widget;

/**
 * ButtonBuilder class file
 * 按钮处理类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ButtonBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class ButtonBuilder extends Widget
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		$label     = isset($this->_tplVars['label'])     ? $this->_tplVars['label']             : '';
		$jsfunc    = isset($this->_tplVars['jsfunc'])    ? trim($this->_tplVars['jsfunc'])      : '';
		$url       = isset($this->_tplVars['url'])       ? $this->_tplVars['url']               : '';
		$primary   = isset($this->_tplVars['primary'])   ? (boolean) $this->_tplVars['primary'] : false;
		$glyphicon = isset($this->_tplVars['glyphicon']) ? trim($this->_tplVars['glyphicon'])   : '';

		$html = $this->getHtml();

		$attributes = array();
		$attributes['type'] = 'button';
		$attributes['class'] = 'btn btn-' . ($primary ? 'primary' : 'default');
		if ($jsfunc !== '') {
			$attributes['onclick'] = 'return ' . $jsfunc . '(\'' . $url . '\');';
		}

		echo $html->openTag('button', $attributes);

		$attributes = array();
		if ($glyphicon !== '') {
			$attributes['class'] = 'glyphicon glyphicon-' . $glyphicon;
		}

		echo $html->tag('span', $attributes, ''), "\n";
		echo $label;

		echo $html->closeTag('button'), "\n";
	}
}
