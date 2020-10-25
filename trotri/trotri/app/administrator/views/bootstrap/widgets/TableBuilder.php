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

use tfc\ap\ErrorException;
use tfc\mvc\Widget;
use library\BaseModel;
use views\bootstrap\components\TableRender;

/**
 * TableBuilder class file
 * 表格处理类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class TableBuilder extends Widget
{
	/**
	 * @var array 寄存表格属性
	 */
	public $attributes = array('class' => 'table table-striped table-hover');

	/**
	 * @var string 表格首列全选按钮Value值，为空则不需要全选按钮
	 */
	public $checkedToggle = '';

	/**
	 * @var array 寄存所有表单元素
	 */
	protected $_elements = array();

	/**
	 * @var \library\BaseModel 表单元素管理类
	 */
	public $elements_object = null;

	/**
	 * @var \views\bootstrap\components\TableRender 表格渲染类
	 */
	public $table_render = null;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::run()
	 */
	public function run()
	{
		// 初始化表格渲染类
		$this->initTableRender();

		// 初始化表单元素管理类
		$this->initElementsObject();

		// 初始化所有表单元素
		$this->initElements();

		if (isset($this->_tplVars['checkedToggle'])) {
			$this->checkedToggle = trim($this->_tplVars['checkedToggle']);
			unset($this->_tplVars['checkedToggle']);
		}

		echo $this->getHtml()->openTag('table', $this->attributes) . "\n";
		echo $this->getThead();
		echo $this->getTbody();
		echo $this->getHtml()->closeTag('table') . "\n";
	}

	/**
	 * 获取Html表格元素：<thead></thead>
	 * @return string
	 */
	public function getThead()
	{
		$html = $this->getHtml();

		$output = $html->openTag('thead') . $html->openTag('tr') . "\n";
		if ($this->checkedToggle !== '') {
			$output .= $html->tag('th', array(), $html->checkbox('checked_toggle', $this->checkedToggle . '[]')) . "\n";
		}

		foreach ($this->_elements as $columnName => $element) {
			$output .= $html->tag('th', $element['attributes'], $element['label']);
		}

		$output .= "\n" . $html->closeTag('tr') . $html->closeTag('thead') . "\n";
		return $output;
	}

	/**
	 * 获取Html表格元素：<tbody></tbody>
	 * @return string
	 */
	public function getTbody()
	{
		$html = $this->getHtml();
		$data = isset($this->_tplVars['data']) ? (array) $this->_tplVars['data'] : array();

		$output = $html->openTag('tbody') . "\n";
		foreach ($data as $row) {
			$output .= $html->openTag('tr') . "\n";

			if ($this->checkedToggle !== '') {
				if (isset($row[$this->checkedToggle])) {
					$output .= $html->tag('td', array(), $html->checkbox($this->checkedToggle . '[]', $row[$this->checkedToggle])) . "\n";
				}
				else {
					throw new ErrorException(sprintf(
						'TableBuilder is unable to find the checked toggle value by key "%s".', $this->checkedToggle
					));
				}
			}

			foreach ($this->_elements as $columnName => $element) {
				if ($element['callback'] !== null) {
					if (is_string($element['callback'])) {
						$value = @call_user_func(array($this->table_render, $element['callback']), $row);
					}
					elseif (is_array($element['callback'])) {
						$value = @call_user_func($element['callback'], $row);
					}
				}
				elseif (isset($row[$columnName])) {
					$value = $row[$columnName];
				}
				else {
					$value = '';
				}

				$output .= $html->tag('td', array(), $value) . "\n";
			}

			$output .= $html->closeTag('tr') . "\n";
		}

		$output .= $html->closeTag('tbody') . "\n";
		return $output;
	}

	/**
	 * 获取Html表格元素：<tfoot></tfoot>
	 * @return string
	 */
	public function getTfoot()
	{
	}

	/**
	 * 初始化表单元素管理类
	 * @return \views\bootstrap\widgets\TableBuilder
	 */
	public function initElementsObject()
	{
		$this->elements_object = $this->table_render->elements_object;
		if ($this->elements_object === null || !$this->elements_object instanceof BaseModel) {
			throw new ErrorException('TableBuilder elements_object is not instanceof library\BaseModel.');
		}

		return $this;
	}

	/**
	 * 初始化表格渲染类
	 * @return \views\bootstrap\widgets\TableBuilder
	 */
	public function initTableRender()
	{
		if (isset($this->_tplVars['table_render'])) {
			$this->table_render = $this->_tplVars['table_render'];
			unset($this->_tplVars['table_render']);
		}

		if ($this->table_render === null || !$this->table_render instanceof TableRender) {
			throw new ErrorException('TableBuilder table_render is not instanceof views\bootstrap\components\TableRender.');
		}

		return $this;
	}

	/**
	 * 初始化表单元素
	 * @return \views\bootstrap\widgets\TableBuilder
	 */
	public function initElements()
	{
		$elements = $this->elements_object->getElementsRender();
		if ($elements === array()) {
			return $this;
		}

		$extends = isset($this->_tplVars['elements']) ? (array) $this->_tplVars['elements'] : array();

		$columns = isset($this->_tplVars['columns']) ? (array) $this->_tplVars['columns'] : array();
		if ($columns === array()) {
			return $this;
		}

		foreach ($columns as $columnName) {
			if ($columnName === '_operate_') {
				$element = array(
					'label' => $this->table_render->view->CFG_SYSTEM_GLOBAL_OPERATE,
					'callback' => 'getOperate'
				);
			}
			else {
				$element = isset($elements[$columnName]) ? $elements[$columnName] : array();
				if (!is_array($element)) {
					continue;
				}

				if (isset($extends[$columnName]) && is_array($extends[$columnName])) {
					$element = array_merge($element, $extends[$columnName]);
				}
			}

			$this->_elements[$columnName] = array(
				'name'       => isset($element['name']) ? $element['name'] : $columnName,
				'label'      => isset($element['label']) ? $element['label'] : '',
				'attributes' => isset($element['attributes']) ? (array) $element['attributes'] : array(),
				'callback'   => isset($element['callback']) ? $element['callback'] : null,
			);
		}

		return $this;
	}
}
