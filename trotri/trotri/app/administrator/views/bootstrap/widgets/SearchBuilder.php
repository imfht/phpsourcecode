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

use tfc\ap\Ap;
use tfc\ap\ErrorException;
use tfc\mvc\form;
use tfc\saf\Text;
use library\BaseModel;

/**
 * SearchBuilder class file
 * 查询表单处理类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: SearchBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class SearchBuilder extends form\FormBuilder
{
	/**
	 * @var string 表单的提交方式
	 */
	public $method = 'get';

	/**
	 * @var \library\BaseModel 表单元素管理类
	 */
	public $elements_object = null;

	/**
	 * @var array 类型和Element关联表
	 */
	protected static $_typeObjectMap = array(
		'text'     => 'views\\bootstrap\\components\\form\\SearchElement',
		'select'   => 'views\\bootstrap\\components\\form\\SearchElement',
		'hidden'   => 'views\\bootstrap\\components\\form\\HiddenElement',
		'datetimepicker' => 'views\\bootstrap\\components\\form\\SearchElement',
	);

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::_init()
	 */
	protected function _init()
	{
		// 初始化表单Action
		if (isset($this->_tplVars['action'])) {
			$this->action = $this->_tplVars['action'];
			unset($this->_tplVars['action']);
		}

		// 初始化表单元素管理类
		$this->initElementsObject();

		parent::_init();
	}

	/**
	 * 初始化表单元素
	 * @return \views\bootstrap\widgets\SearchBuilder
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

		$_elements = array();
		foreach ($columns as $columnName) {
			if (!isset($elements[$columnName])) {
				continue;
			}

			$element = $elements[$columnName];
			if (!is_array($element)) {
				continue;
			}

			if (isset($extends[$columnName]) && is_array($extends[$columnName])) {
				$element = array_merge($element, $extends[$columnName]);
			}

			$object = isset($element['__object__']) ? $element['__object__'] : '';
			$type = isset($element['type']) ? $element['type'] : 'text';
			if ($object === '' && isset(self::$_typeObjectMap[$type])) {
				$object = self::$_typeObjectMap[$type];
			}

			$placeholder = isset($element['placeholder']) ? $element['placeholder'] : (isset($element['label']) ? $element['label'] : '');
			$options = isset($element['options']) ? (array) $element['options'] : array();
			if ($options !== array() && $placeholder !== '') {
				$options = array('' => '--' . $placeholder . '--') + $options;
			}

			$_elements[$columnName] = array(
				'type' => $type,
				'__object__' => $object,
				'placeholder' => $placeholder,
				'options' => $options
			);
		}

		// 设置查询按钮
		$_elements['_button_search_'] = array(
			'type' => 'button',
			'__object__' => 'views\\bootstrap\\components\\form\\ButtonElement',
			'label' => Text::_('CFG_SYSTEM_GLOBAL_SEARCH'),
			'glyphicon' => 'search',
			'class' => 'btn btn-primary btn-block'
		);

		parent::setElements($_elements);
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::initValues()
	 */
	public function initValues()
	{
		if (isset($this->_tplVars['values'])) {
			if (is_array($this->_tplVars['values'])) {
				$this->values = $this->_tplVars['values'];
				unset($this->_tplVars['values']);
			}
			else {
				throw new ErrorException('SearchBuilder TplVars.values invalid, values must be array.');
			}
		}
		else {
			$this->values = Ap::getRequest()->getQuery();
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::run()
	 */
	public function run()
	{
		parent::run();
		$this->displayJs();
	}

	/**
	 * 将JS内容输出到浏览器
	 * @return void
	 */
	public function displayJs()
	{
		$this->assign('id', $this->getId());
		$this->display($this->getJsName());
	}

	/**
	 * 初始化表单元素管理类
	 * @return \views\bootstrap\widgets\SearchBuilder
	 */
	public function initElementsObject()
	{
		if (isset($this->_tplVars['elements_object'])) {
			$this->elements_object = $this->_tplVars['elements_object'];
			unset($this->_tplVars['elements_object']);
		}

		if ($this->elements_object === null || !$this->elements_object instanceof BaseModel) {
			throw new ErrorException('FormBuilder elements_object is not instanceof library\BaseModel.');
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\Widget::getWidgetDirectory()
	 */
	public function getWidgetDirectory()
	{
		if ($this->_widgetDirectory === null) {
			$this->_widgetDirectory = dirname(__FILE__) . DS . 'searchbuilder';
		}

		return $this->_widgetDirectory;
	}
}
