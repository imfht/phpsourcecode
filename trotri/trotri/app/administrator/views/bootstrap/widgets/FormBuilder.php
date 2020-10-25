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
use tfc\mvc\form;
use tfc\saf\Text;
use library\BaseModel;
use views\bootstrap\components\ComponentsBuilder;

/**
 * FormBuilder class file
 * 表单处理类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FormBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class FormBuilder extends form\FormBuilder
{
	/**
	 * @var string 默认的分类标签
	 */
	const DEFAULT_TID = 'main';

	/**
	 * @var array 寄存表单属性
	 */
	public $attributes = array('class' => 'form-horizontal');

	/**
	 * @var \library\BaseModel 表单元素管理类
	 */
	public $elements_object = null;

	/**
	 * @var array Input表单元素分类标签
	 */
	protected $_tabs = array(
		'main' => array('tid' => 'main', 'prompt' => 'Main', 'active' => true)
	);

	/**
	 * @var array 类型和Element关联表
	 */
	protected static $_typeObjectMap = array(
		'text'     => 'InputElement',
		'password' => 'InputElement',
		'file'     => 'InputElement',
		'button'   => 'ButtonElement',
		'hidden'   => 'HiddenElement',
		'checkbox' => 'ICheckboxElement',
		'radio'    => 'IRadioElement',
		'switch'   => 'SwitchElement',
		'textarea' => 'TextareaElement',
		'select'   => 'SelectElement',
		'string'   => 'StringElement',
		'ckeditor' => 'CkeditorElement',
		'datetimepicker' => 'DatetimepickerElement'
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

		// 初始化分类标签
		$this->_tabs['main']['prompt'] = Text::_('CFG_SYSTEM_GLOBAL_VIEWTAB_MAIN_PROMPT');
		$this->setTabs($this->elements_object->getViewTabsRender());

		parent::_init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::run()
	 */
	public function run()
	{
		$this->assign('tabs', $this->getTabs());
		parent::run();
		$this->displayJs();
	}

	/**
	 * 获取所有Input表单元素分类标签
	 * @return array
	 */
	public function getTabs()
	{
		return $this->_tabs;
	}

	/**
	 * 设置多个Input表单元素分类标签
	 * @param array $tabs
	 * @return \views\bootstrap\widgets\FormBuilder
	 */
	public function setTabs(array $tabs = array())
	{
		foreach ($tabs as $tid => $tab) {
			$prompt = isset($tab['prompt']) ? $tab['prompt'] : '';
			$active = isset($tab['active']) ? $tab['active'] : false;
			$this->addTab($tid, $prompt, $active);
		}

		return $this;
	}

	/**
	 * 清除所有的分类标签
	 * @return \views\bootstrap\widgets\FormBuilder
	 */
	public function clearTabs()
	{
		$this->_tabs = array();
		return $this;
	}

	/**
	 * 通过分类ID获取Input表单元素分类标签
	 * @param string $tid
	 * @return array
	 */
	public function getTabByTid($tid)
	{
		return $this->hasTab($tid) ? $this->_tabs[$tid] : null;
	}

	/**
	 * 添加或修改Input表单元素分类标签
	 * @param string $tid
	 * @param string $prompt
	 * @param boolean $active
	 * @return \views\bootstrap\widgets\FormBuilder
	 */
	public function addTab($tid, $prompt, $active = false)
	{
		if (($tid = trim($tid)) === '') {
			return $this;
		}

		$this->_tabs[$tid] = array(
			'tid' => $tid,
			'prompt' => $prompt,
			'active' => (boolean) $active
		);

		return $this;
	}

	/**
	 * 通过分类ID判断该表单元素分类标签是否已经存在
	 * @param string $tid
	 * @return boolean
	 */
	public function hasTab($tid)
	{
		return isset($this->_tabs[$tid]);
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
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::getInputs()
	 */
	public function getInputs()
	{
		$output = array();
		$tabs = $this->getTabs();
		foreach ($tabs as $tid => $tab) {
			$output[$tid] = $this->getInputsByTid($tid);
		}

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::initElements()
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
			if (substr($columnName, 0, 8) === '_button_') {
				$funcName = 'get' . str_replace('_', '', $columnName);
				if ($columnName === '_button_cancel_') {
					$element = ComponentsBuilder::$funcName($this->elements_object->getLLU());
				}
				else {
					$element = ComponentsBuilder::$funcName();
				}
			}
			else {
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
			}

			if (!isset($element['__object__']) && isset($element['type'])) {
				$type = $element['type'];
				if (isset(self::$_typeObjectMap[$type])) {
					$element['__object__'] = self::$_typeObjectMap[$type];
				}
			}

			if (!isset($element['__tid__'])) {
				$element['__tid__'] = self::DEFAULT_TID;
			}

			$_elements[$columnName] = $element;
		}

		parent::setElements($_elements);
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::createElement()
	 */
	public function createElement($className, array $config = array())
	{
		if (strpos($className, '\\') === false) {
			$className = 'views\\bootstrap\\components\\form\\' . $className;
		}

		return parent::createElement($className, $config);
	}

	/**
	 * 初始化表单元素管理类
	 * @return \views\bootstrap\widgets\FormBuilder
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
			$this->_widgetDirectory = dirname(__FILE__) . DS . 'formbuilder';
		}

		return $this->_widgetDirectory;
	}
}
