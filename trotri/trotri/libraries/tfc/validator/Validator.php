<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\validator;

/**
 * Validator abstract class file
 * 数据验证基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Validator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
abstract class Validator
{
    /**
     * @var mixed 需要验证的值
     */
    protected $_value = null;

    /**
     * @var mixed 记录验证项
     */
    protected $_option = null;

    /**
     * @var string 出错后的提醒消息
     */
    protected $_message = '';

    /**
     * 构造方法：初始化需要验证的值、验证参考内容、出错后返回的消息
     * @param mixed $value
     * @param mixed $option
     * @param string $message
     */
    public function __construct($value, $option, $message = '')
    {
        $this->init($value, $option, $message);
    }

    /**
     * 初始化需要验证的值、验证参考内容、出错后返回的消息
     * @param mixed $value
     * @param mixed $option
     * @param string $message
     * @return \tfc\validator\Validator
     */
    public function init($value, $option, $message = '')
    {
        $this->setValue($value);
        $this->setOption($option);
        $this->setMessage($message);
        return $this;
    }

    /**
     * 验证数据格式
     * @return boolean
     */
    public abstract function isValid();

    /**
     * 获取需要验证的值
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * 设置需要验证的值
     * @param mixed $value
     * @return \tfc\validator\Validator
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * 获取验证参考内容
     * @return mixed
     */
    public function getOption()
    {
        return $this->_option;
    }

    /**
     * 设置验证参考内容
     * @param mixed $value
     * @return \tfc\validator\Validator
     */
    public function setOption($value)
    {
        $this->_option = $value;
        return $this;
    }

    /**
     * 获取出错后返回的消息
     * @return string
     */
    public function getMessage()
    {
        $message = str_replace(
            array('%value%', '%option%'),
            array(
                $this->getValue(),
                (is_array($option = $this->getOption())) ? serialize($option) : $option
            ),
            $this->_message
        );

        return $message;
    }

    /**
     * 设置出错后返回的消息
     * @param string $value
     * @return \tfc\validator\Validator
     */
    public function setMessage($value)
    {
        if (($value = trim($value)) != '') {
            $this->_message = $value;
        }

        return $this;
    }
}
