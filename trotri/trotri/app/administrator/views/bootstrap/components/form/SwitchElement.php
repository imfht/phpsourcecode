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

use tfc\saf\Text;

/**
 * SwitchElement class file
 * 美化版“是|否”选择项表单元素，基于Bootstrap-v3前端开发框架的switch开关控件
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: SwitchElement.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components.form
 * @since 1.0
 */
class SwitchElement extends InputElement
{
	/**
	 * @var boolean 是否被选中
	 */
	public $checked = false;

	/**
	 * @var string 表单元素的样式
	 */
	public $swClass = 'switch';

	/**
	 * @var string 选项ID
	 */
	public $swId = 'label_switch';

	/**
	 * @var string 选项“是”标签
	 */
	public $swOn = '';

	/**
	 * @var string 选项“否”标签
	 */
	public $swOff = '';

	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\components\form\InputElement::_init()
	 */
	protected function _init()
	{
		$this->swOn = Text::_('CFG_SYSTEM_GLOBAL_YES');
		$this->swOff = Text::_('CFG_SYSTEM_GLOBAL_NO');
		parent::_init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\InputElement::getInput()
	 */
	public function getInput()
	{
		$name = $this->getName(true);

		$attributes = array(
			'id'             => $this->swId . '_' . $name,
			'class'          => $this->swClass,
			'data-on-label'  => $this->swOn,
			'data-off-label' => $this->swOff,
			'name'           => 'label_switch',
		);

		if ($this->value === 'y') {
			$this->checked = true;
		}

		$html = $this->getHtml();
		return $html->tag('div', $attributes, $html->checkbox($name, $this->value, $this->checked));
	}
}
