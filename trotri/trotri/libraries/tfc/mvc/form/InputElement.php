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

/**
 * InputElement class file
 * 输入框类表单元素
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: InputElement.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.form
 * @since 1.0
 */
class InputElement extends Element
{
    /**
     * @var array 选项类表单元素多选项值，array('value' => 'prompt')
     */
    public $options = array();

    /**
     * @var string Label名
     */
    public $label = '';

    /**
     * @var string 用户输入提示
     */
    public $hint = '';

    /**
     * @var string 错误提示
     */
    public $error = '';

    /**
     * @var string 页面布局，{prompt}是{hint}或{error}其中之一
     */
    public $layout = "{label}\n{input}\n{prompt}";

    /**
     * @var string 错误提示样式名
     */
    protected $_errorClassName = '';

    /**
     * @var string 隐藏表单样式名
     */
    protected $_hiddenClassName = '';

    /**
     * @var boolean 是否显示
     */
    protected $_visible = true;

    /**
     * @var boolean 是否必填
     */
    protected $_required = false;

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\form\Element::fetch()
     */
    public function fetch()
    {
        $output = array(
            '{label}' => $this->openLabel() . $this->getLabel() . $this->closeLabel(),
            '{input}' => $this->openInput() . $this->getInput() . $this->closeInput(),
            '{prompt}' => $this->openPrompt() . $this->getPrompt() . $this->closePrompt(),
        );

        return $this->openWrap() . strtr($this->layout, $output) . $this->closeWrap();
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\form\Element::getInput()
     */
    public function getInput()
    {
        $type = $this->getType();
        $name = $this->getName(true);
        $attributes = $this->getAttributes();
        $html = $this->getHtml();

        $output = '';
        if ($type === 'select') {
            $output .= $html->openSelect($name, $attributes);
            $output .= $html->options($this->options, $this->value);
            $output .= $html->closeSelect();
        }
        elseif ($type === 'radio' || $type === 'checkbox') {
            foreach ($this->options as $value => $prompt) {
                $checked = ($value === $this->value) ? true : false;
                $output .= $html->$type($name, $value, $checked, $attributes) . $prompt;
            }
        }
        else {
            $output .= $html->$type($name, $this->value, $attributes);
        }

        return $output;
    }

    /**
     * 获取Label-HTML
     * @return string
     */
    public function getLabel()
    {
        return $this->label . ($this->getRequired() ? ' *' : '');
    }

    /**
     * 获取Prompt(Hint|Error)-HTML
     * @return string
     */
    public function getPrompt()
    {
        return $this->hasError() ? $this->error : $this->hint;
    }

    /**
     * 获取表单元素最外层HTML开始标签
     * @return string
     */
    public function openWrap()
    {
        return $this->getHtml()->openTag('div') . "\n";
    }

    /**
     * 获取表单元素最外层HTML结束标签
     * @return string
     */
    public function closeWrap()
    {
        return "\n" . $this->getHtml()->closeTag('div');
    }

    /**
     * 获取Label-HTML开始标签
     * @return string
     */
    public function openLabel()
    {
        return '';
    }

    /**
     * 获取Label-HTML结束标签
     * @return string
     */
    public function closeLabel()
    {
        return '';
    }

    /**
     * 获取表单元素Input-HTML开始标签
     * @return string
     */
    public function openInput()
    {
        return '';
    }

    /**
     * 获取表单元素Input-HTML结束标签
     * @return string
     */
    public function closeInput()
    {
        return '';
    }

    /**
     * 获取用户输入提示和错误提示-HTML开始标签
     * @return string
     */
    public function openPrompt()
    {
        return '';
    }

    /**
     * 获取用户输入提示和错误提示-HTML结束标签
     * @return string
     */
    public function closePrompt()
    {
        return '';
    }

    /**
     * 判断是否有错误提示
     * @return boolean
     */
    public function hasError()
    {
        return $this->error !== '';
    }

    /**
     * 获取是否显示
     * @return boolean
     */
    public function getVisible()
    {
        return $this->_visible;
    }

    /**
     * 设置是否显示
     * @param boolean $value
     * @return \tfc\mvc\form\InputElement
     */
    public function setVisible($value)
    {
        $this->_visible = (boolean) $value;
        return $this;
    }

    /**
     * 获取是否必填
     * @return boolean
     */
    public function getRequired()
    {
        return $this->_required;
    }

    /**
     * 设置是否必填
     * @param boolean $value
     * @return \tfc\mvc\form\InputElement
     */
    public function setRequired($value)
    {
        $this->_required = (boolean) $value;
        return $this;
    }
}
