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
 * CkeditorElement class file
 * Ckeditor表单元素，基于ckeditor_4.4.4开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: CkeditorElement.php 1 2013-10-30 23:11:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class CkeditorElement extends TextareaElement
{
	/**
	 * @var string ID
	 */
	protected $_id = 'ckeditor';

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::_init()
	 */
	protected function _init()
	{
		$this->setAttribute('id', $this->_id);
		parent::_init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\TextareaElement::getInput()
	 */
	public function getInput()
	{
		$this->setType('textarea');

		$id = $this->getAttribute('id');
		$height = $this->getAttribute('height', null);
		$width = $this->getAttribute('width', null);
		$toolbar = $this->getAttribute('toolbar', null);
		$filebrowserUploadUrl = $this->getAttribute('url', null);

		$html = $this->getHtml();
		$output = parent::getInput();

		$json = array();
		if ($width) {
			$json[] = 'width: "' . $width . '"';
		}

		if ($height) {
			$json[] = 'height: "' . $height . '"';
		}

		if ($toolbar) {
			$json[] = 'toolbar: Core.ckeditor.toolbar.' . $toolbar;
		}

		if ($filebrowserUploadUrl) {
			$json[] = 'filebrowserUploadUrl: "' . $filebrowserUploadUrl . '"';
		}

		$script = '$(function() { CKEDITOR.replace( "' . $id . '", { ' . implode(', ', $json) . ' } ); });';
		$output .= $html->js($script);
		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::openLabel()
	 */
	public function openLabel()
	{
		return $this->getHtml()->openTag('label', array('class' => 'col-lg-1 control-label'));
	}

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::openInput()
	 */
	public function openInput()
	{
		return $this->getHtml()->openTag('div', array('class' => 'col-lg-11')) . "\n";
	}
}
