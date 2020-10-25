<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc\form;

use tfc\mvc\Widget;
use tfc\ap\Ap;
use tfc\ap\Registry;
use tfc\ap\ErrorException;

/**
 * FormBuilder abstract class file
 * 表单处理基类，需要加载模板文件才能生成表单
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FormBuilder.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.form
 * @since 1.0
 */
abstract class FormBuilder extends Widget
{
    /**
     * @var string 表单的名称
     */
    public $name = '';

    /**
     * @var string 表单的提交链接
     */
    public $action = '';

    /**
     * @var string 表单的提交方式
     */
    public $method = 'post';

    /**
     * @var array 寄存表单属性
     */
    public $attributes = array();

    /**
     * @var boolean 表单数据是否二进制提交
     */
    public $multipart = false;

    /**
     * @var array 寄存所有表单元素的默认值
     */
    public $values = array();

    /**
     * @var array 寄存所有表单元素的错误提示
     */
    public $errors = array();

    /**
     * @var array 寄存所有输入框类和字符串类表单元素
     */
    protected $_inputElements = array();

    /**
     * @var array 寄存所有按钮类表单元素
     */
    protected $_buttonElements = array();

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\Widget::_init()
     */
    protected function _init()
    {
        $this->initValues();
        $this->initErrors();
        $this->initElements();

        foreach ($this->_tplVars as $key => $value) {
            $this->$key = $value;
        }

        $this->initId();
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\Widget::run()
     */
    public function run()
    {
        $this->assign('form_inputs', $this->getInputs());
        $this->assign('form_buttons', $this->getButtons());

        $this->assign('form_open', $this->openForm());
        $this->assign('form_close', $this->closeForm());

        $this->display();
    }

    /**
     * 获取所有的输入框类和字符串类表单元素HTML
     * @return string
     */
    public function getInputs()
    {
        return $this->getInputsByTid();
    }

    /**
     * 获取一组输入框类和字符串类表单元素HTML
     * @param string $tid
     * @return string
     */
    public function getInputsByTid($tid = '')
    {
        $output = '';
        $inputElements = $this->getInputElements($tid);
        foreach ($inputElements as $inputElement) {
            $output .= $inputElement->fetch() . "\n";
        }

        return $output;
    }

    /**
     * 获取所有按钮HTML
     * @return string
     */
    public function getButtons()
    {
        $output = '';
        $buttonElements = $this->getButtonElements();
        foreach ($buttonElements as $buttonElement) {
            $output .= $buttonElement->fetch();
        }

        return $output;
    }

    /**
     * 设置多个表单元素
     * @param array $elements
     * @return \tfc\mvc\form\FormBuilder
     * @throws ErrorException 如果获取的实例不是tfc\mvc\form\InputElement或tfc\mvc\form\ButtonElement类的子类，抛出异常
     */
    public function setElements(array $elements = array())
    {
        foreach ($elements as $name => $element) {
            if (!isset($element['__object__'])) {
                continue;
            }

            $className = $element['__object__']; unset($element['__object__']);
            $tid = '';
            if (isset($element['__tid__'])) {
                $tid = $element['__tid__']; unset($element['__tid__']);
            }

            if (!isset($element['name'])) {
                $element['name'] = $name;
            }

            $instance = $this->createElement($className, $element);
            if ($instance instanceof InputElement) {
                $this->addInputElement($instance, $tid);
            }
            elseif ($instance instanceof ButtonElement) {
                $this->addButtonElement($instance);
            }
            else {
                throw new ErrorException(sprintf(
                    'FormBuilder Element class "%s" is not instanceof tfc\mvc\form\InputElement or tfc\mvc\form\ButtonElement.', $className
                ));
            }
        }

        return $this;
    }

    /**
     * 获取所有输入框类和字符串类表单元素
     * @param string $tid
     * @return array
     */
    public function getInputElements($tid = '')
    {
        if ($tid !== '') {
            return isset($this->_inputElements[$tid]) ? $this->_inputElements[$tid] : array();
        }

        return $this->_inputElements;
    }

    /**
     * 添加输入框类和字符串类表单元素
     * @param InputElement $element
     * @param string $tid
     * @return \tfc\mvc\form\FormBuilder
     */
    public function addInputElement(InputElement $element, $tid = '')
    {
        $name = $element->getName(true);

        if (isset($this->values[$name])) {
            $element->value = $this->values[$name];
        }

        if (isset($this->errors[$name])) {
            $element->error = $this->errors[$name];
        }

        if ($tid !== '') {
            $this->_inputElements[$tid][] = $element;
        }
        else {
            $this->_inputElements[] = $element;
        }

        return $this;
    }

    /**
     * 获取所有的按钮类表单元素
     * @return array
     */
    public function getButtonElements()
    {
        return $this->_buttonElements;
    }

    /**
     * 添加按钮类表单元素
     * @param ButtonElement $element
     * @return \tfc\mvc\form\FormBuilder
     */
    public function addButtonElement(ButtonElement $element)
    {
        $this->_buttonElements[] = $element;
        return $this;
    }

    /**
     * 创建表单元素类
     * @param string $className
     * @param array $config
     * @return \tfc\mvc\form\Element
     * @throws ErrorException 如果Element类不存在，抛出异常
     * @throws ErrorException 如果获取的实例不是tfc\mvc\form\Element类的子类，抛出异常
     */
    public function createElement($className, array $config = array())
    {
        if (!class_exists($className)) {
            throw new ErrorException(sprintf(
                'FormBuilder is unable to find the requested element "%s".', $className
            ));
        }

        $instance = new $className($config);
        if (!$instance instanceof Element) {
            throw new ErrorException(sprintf(
                'FormBuilder Element class "%s" is not instanceof tfc\mvc\form\Element.', $className
            ));
        }

        return $instance;
    }

    /**
     * 获取Form开始标签
     * @return string
     */
    public function openForm()
    {
        $this->attributes['name'] = $this->name;
        if ($this->multipart) {
            return $this->getHtml()->openFormMultipart($this->action, $this->attributes);
        }

        return $this->getHtml()->openForm($this->action, $this->method, $this->attributes);
    }

    /**
     * 获取Form结束标签
     * @return string
     */
    public function closeForm()
    {
        return $this->getHtml()->closeForm();
    }

    /**
     * 初始化所有表单元素的默认值
     * @return \tfc\mvc\form\FormBuilder
     * @throws ErrorException 如果默认值不是数组，抛出异常
     */
    public function initValues()
    {
        if (isset($this->_tplVars['values'])) {
            if (!is_array($this->_tplVars['values'])) {
                throw new ErrorException('FormBuilder TplVars.values invalid, values must be array.');
            }

            $this->values = $this->_tplVars['values'];
            unset($this->_tplVars['values']);
        }
        else {
            $this->values = Ap::getRequest()->getPost();
        }

        return $this;
    }

    /**
     * 初始化所有表单元素的错误提示
     * @return \tfc\mvc\form\FormBuilder
     * @throws ErrorException 如果错误提示不是数组，抛出异常
     */
    public function initErrors()
    {
        if (isset($this->_tplVars['errors'])) {
            if (!is_array($this->_tplVars['errors'])) {
                throw new ErrorException('FormBuilder TplVars.errors invalid, errors must be array.');
            }

            $this->errors = $this->_tplVars['errors'];
            unset($this->_tplVars['errors']);
        }

        return $this;
    }

    /**
     * 初始化所有表单元素
     * @return \tfc\mvc\form\FormBuilder
     * @throws ErrorException 如果模板参数elements不存在或不是数组，抛出异常
     */
    public function initElements()
    {
        if (!isset($this->_tplVars['elements'])) {
            throw new ErrorException('FormBuilder TplVars.elements was not defined.');
        }

        if (!is_array($this->_tplVars['elements'])) {
            throw new ErrorException('FormBuilder TplVars.elements invalid, elements must be array.');
        }

        $this->setElements($this->_tplVars['elements']);
        unset($this->_tplVars['elements']);

        return $this;
    }

    /**
     * 初始化表单ID
     * @return \tfc\mvc\form\FormBuilder
     */
    public function initId()
    {
        if (isset($this->attributes['id'])) {
            return $this;
        }

        $id = (int) Registry::get('FormBuilder_ID') + 1;
        Registry::set('FormBuilder_ID', $id);

        $this->attributes['id'] = 'form_id_' . $id;
        return $this;
    }

    /**
     * 返回表单ID
     * @return string
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : '';
    }
}
