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

use tfc\ap\ErrorException;
use tfc\ap\Singleton;

/**
 * Element abstract class file
 * 表单元素基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Element.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.form
 * @since 1.0
 */
abstract class Element
{
    /**
     * @var string 表单元素的默认值
     */
    public $value = '';

    /**
     * @var string 表单元素的类型
     */
    protected $_type = '';

    /**
     * @var string 表单元素的名称
     */
    protected $_name = '';

    /**
     * @var array 寄存表单元素属性
     */
    protected $_attributes = array();

    /**
     * 构造方法：初始化所有属性值
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->_init();
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * 子类构造方法：子类调用此方法作为构造方法，避免重写父类构造方法
     */
    protected function _init()
    {
    }

    /**
     * 魔术方法：请求get开头的方法，获取一个受保护的属性值；或者从attributes中获取表单元素属性
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->getAttribute($name);
    }

    /**
     * 魔术方法：请求set开头的方法，设置一个受保护的属性值；或者将表单元素属性设置到attributes中
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $this->setAttribute($name, $value);
    }

    /**
     * 获取表单元素的类型
     * @return string
     * @throws ErrorException 如果该表单元素的类型为空，抛出异常
     */
    public function getType()
    {
        if ($this->_type === '') {
            throw new ErrorException('Element no type is registered.');
        }

        return $this->_type;
    }

    /**
     * 设置表单元素的类型
     * @param string $value
     * @return \tfc\mvc\form\Element
     */
    public function setType($value)
    {
        $this->_type = trim($value);
        return $this;
    }

    /**
     * 获取表单元素的名称
     * @param boolean $throwException
     * @return string
     * @throws ErrorException 如果该表单元素的类型为空并且需要抛出异常，抛出异常
     */
    public function getName($throwException = false)
    {
        if ($this->_name === '' && $throwException) {
            throw new ErrorException('Element no name is registered.');
        }

        return $this->_name;
    }

    /**
     * 设置表单元素的名称
     * @param string $value
     * @return \tfc\mvc\form\Element
     */
    public function setName($value)
    {
        $this->_name = trim($value);
        return $this;
    }

    /**
     * 获取所有的表单元素属性
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * 获取一个表单元素属性
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = '')
    {
        return $this->hasAttribute($name) ? $this->_attributes[$name] : $default;
    }

    /**
     * 设置一个表单元素属性
     * @param string $name
     * @param mixed $value
     * @return \tfc\mvc\form\Element
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    /**
     * 删除一个表单元素属性
     * @param string $name
     * @return \tfc\mvc\form\Element
     */
    public function removeAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            unset($this->_attributes[$name]);
        }

        return $this;
    }

    /**
     * 获取表单元素属性是否存在
     * @param string $name
     * @return boolean
     */
    public function hasAttribute($name)
    {
        return isset($this->_attributes[$name]);
    }

    /**
     * 获取表单元素样式名
     * @return string
     */
    public function getClass()
    {
        return $this->getAttribute('class');
    }

    /**
     * 设置表单元素样式名
     * @param string $value
     * @return \tfc\mvc\form\Element
     */
    public function setClass($value)
    {
        $this->setAttribute('class', $value);
        return $this;
    }

    /**
     * 获取表单元素是否只读
     * @return boolean
     */
    public function getReadonly()
    {
        return (boolean) $this->getAttribute('readonly');
    }

    /**
     * 设置表单元素是否只读
     * @param boolean $value
     * @return \tfc\mvc\form\Element
     */
    public function setReadonly($value)
    {
        $value ? $this->setAttribute('readonly', 'readonly') : $this->removeAttribute('readonly');
        return $this;
    }

    /**
     * 获取表单元素是否禁用
     * @return boolean
     */
    public function getDisabled()
    {
        return (boolean) $this->getAttribute('disabled');
    }

    /**
     * 设置表单元素是否禁用
     * @param boolean $value
     * @return \tfc\mvc\form\Element
     */
    public function setDisabled($value)
    {
        $value ? $this->setAttribute('disabled', 'disabled') : $this->removeAttribute('disabled');    
        return $this;
    }

    /**
     * 获取页面辅助类
     * @return \tfc\mvc\Html
     */
    public function getHtml()
    {
        return Singleton::getInstance('tfc\\mvc\\Html');
    }

    /**
     * 魔术方法：获取表单HTML内容
     * @return string
     */
    public function __toString()
    {
        return $this->fetch();
    }

    /**
     * 输出表单HTML内容
     * @return void
     */
    public function display()
    {
        echo $this->fetch();
    }

    /**
     * 获取表单HTML内容，需要通过子类重写
     * @return string
     */
    abstract public function fetch();

    /**
     * 获取表单元素（Input、Button、Select等）HTML内容，需要通过子类重写
     * @return string
     */
    abstract public function getInput();
}
