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
 * TextareaElement class file
 * Textarea表单元素，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TextareaElement.php 1 2013-10-30 23:11:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class TextareaElement extends InputElement
{
	/**
	 * @var integer 文本框的行数
	 */
	public $rows = 5;

	/**
	 * @var integer 文本框的列数
	 */
	public $cols = 0;

	/**
	 * @var string 表单元素的类型
	 */
	protected $_type = 'textarea';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::getInput()
	 */
	public function getInput()
	{
		if ($this->rows > 0) {
			$this->setAttribute('rows', $this->rows);
		}

		if ($this->cols > 0) {
			$this->setAttribute('cols', $this->cols);
		}

		return parent::getInput();
	}
}
