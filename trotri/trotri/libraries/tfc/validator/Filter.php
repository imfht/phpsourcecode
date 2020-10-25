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

use tfc\ap\ErrorException;

/**
 * Filter class file
 * 数据验证类
 *
 * 验证规则：
 * <pre>
 * $rules = array(
 *     'user_loginname' => array(
 *         'MinLength' => array(6, '用户名长度%value%不能小于%option%个字符.'),
 *         'MaxLength' => array(12, '用户名长度%value%不能大于%option%个字符.')
 *     ),
 *     'user_password' => array(
 *         'tfc\\validator\\MinLengthValidator' => array(6, '密码长度%value%不能小于%option%个字符.'),
 *         'MaxLength' => array(12, '密码长度%value%不能大于%option%个字符.')
 *     ),
 *     'user_email' => array(
 *         'MaxLength' => array(50, '邮箱长度%value%不能大于%option%个字符.'),
 *         'Mail' => array(true, '邮箱%value%不符合规范.')
 *     ),
 * );
 *
 * $params = array(
 *     'user_loginname' => 'abcdefghi',
 *     'user_password' => '1234',
 *     'user_email' => 'trotriyeahnet'
 * );
 *
 * $filter = new Filter();
 * $result = $filter->run($rules, $params);
 * $errors = $filter->getErrors();
 * 结果：
 * $errors = array(
 *     'user_password' => '密码长度1234不能小于6个字符.',
 *     'user_email' => array('邮箱长度trotriyeahnet...不能大于50个字符.', '邮箱iphperyeahnet...不符合规范.')
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Filter.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class Filter
{
    /**
     * @var array 寄存所有错误信息
     */
    protected $_errors = array();

    /**
     * 运行验证处理类
     * @param array $rules
     * @param array $attributes
     * @param boolean $required
     * @return boolean
     * @throws ErrorException 如果验证规则或者验证数据不是数组，抛出异常
     * @throws ErrorException 如果字段验证规则不是数组，抛出异常
     * @throws ErrorException 如果键名在验证数据中不存在，抛出异常
     */
    public function run(array $rules, array $attributes, $required = true)
    {
        if ($rules === null || $attributes === null) {
            throw new ErrorException('Filter the rules and attributes must be array.');
        }

        $this->clearErrors();
        foreach ($rules as $columnName => $rule) {
            if (!is_array($rule)) {
                throw new ErrorException(sprintf(
                    'Filter the rule of rules "%s" must be array.', $columnName
                ));
            }

            if (!isset($attributes[$columnName])) {
                if ($required) {
                    $this->addError($columnName, sprintf(
                        'Filter the column name "%s" of attributes is undefined', $columnName
                    ));
                }

                continue;
            }

            foreach ($rule as $validator => $_rule) {
                $instance = $this->createValidator($validator, $attributes[$columnName], $_rule);
                if ($instance->isValid()) {
                    continue;
                }

                $this->addError($columnName, $instance->getMessage());
            }
        }

        return !$this->hasError();
    }

    /**
     * 基于配置清理表单提交的数据
     * <pre>
     * 一.清理规则：
     * $rules = array(
     *     'user_loginname' => 'trim',
     *     'user_interest' => array($foo, 'explode')
     * );
     * 参数：
     * $attributes = array(
     *     'user_loginname' => '  abcdefghi  ',
     *     'user_interest' => ' 1, 2'
     * );
     * 结果：
     * $result = array(
     *     'user_loginname' => 'abcdefghi',
     *     'user_interest' => array(1, 2)
     * );
     *
     * 二.清理规则：
     * $rules = array(
     *     'user_password' => 'md5',
     *     'user_interest' => array($foo, 'implode')
     * );
     * 参数：
     * $attributes = array(
     *     'user_password' => '  1234  ',
     *     'user_interest' => array(1, 2)
     * );
     * 结果：
     * $result = array(
     *     'user_loginname' => '81dc9bdb52d04dc20036dbd8313ed055',
     *     'user_interest' => '1,2'
     * );
     * </pre>
     * @param array $rules
     * @param array $attributes
     * @return array
     */
    public function clean(array $rules, array $attributes)
    {
        if ($rules === null || $attributes === null) {
            return ;
        }

        foreach ($rules as $columnName => $funcName) {
            if (isset($attributes[$columnName])) {
                $attributes[$columnName] = call_user_func($funcName, $attributes[$columnName]);
            }
        }

        return $attributes;
    }

    /**
     * 创建验证类
     * @param string $className
     * @param mixed $value
     * @param mixed $rule
     * @return \tfc\validator\Validator
     * @throws ErrorException 如果Validator类不存在，抛出异常
     */
    public function createValidator($className, $value, $rule)
    {
        $rule = (array) $rule;
        $option = isset($rule[0]) ? $rule[0] : null;
        if ($option === null) {
            throw new ErrorException(
                'Filter option of rules is undefined'
            );
        }

        $message = isset($rule[1]) ? $rule[1] : '';

        if (strpos($className, '\\') === false) {
            $className = 'tfc\\validator\\' . $className . 'Validator';
        }
        if (!class_exists($className)) {
            throw new ErrorException(sprintf(
                'Filter is unable to find the requested validator "%s".', $className
            ));
        }

        return $this->getValidator($className, $value, $option, $message);
    }

    /**
     * 根据类名获取验证类
     * @param string $className
     * @param mixed $value
     * @param mixed $option
     * @param string $message
     * @return \tfc\validator\Validator
     * @throws ErrorException 如果获取的实例不是tfc\validator\Validator类的子类，抛出异常
     */
    public function getValidator($className, $value, $option, $message)
    {
        $instance = new $className($value, $option, $message);
        if (!$instance instanceof Validator) {
            throw new ErrorException(sprintf(
                'Filter Validator class "%s" is not instanceof tfc\validator\Validator.', $className
            ));
        }

        return $instance->init($value, $option, $message);
    }

    /**
     * 获取所有的错误信息
     * @param boolean $justOne
     * @return array
     */
    public function getErrors($justOne = false)
    {
        if (!$justOne) {
            return $this->_errors;
        }

        $errors = array();
        foreach ($this->_errors as $key => $value) {
            $errors[$key] = is_array($value) ? array_shift($value) : $value;
        }

        return $errors;
    }

    /**
     * 清除所有的错误信息
     * @return \tfc\validator\Validator
     */
    public function clearErrors()
    {
        $this->_errors = array();
        return $this;
    }

    /**
     * 通过键名获取错误信息
     * @param string|null $key
     * @param boolean $justOne
     * @return mixed
     */
    public function getError($key = null, $justOne = true)
    {
        if (empty($this->_errors)) {
            return null;
        }

        if ($key === null) {
            return array_shift(array_slice($this->_errors, 0, 1));
        }
        elseif (isset($this->_errors[$key])) {
            return ($justOne && is_array($this->_errors[$key])) ? array_shift($this->_errors[$key]) : $this->_errors[$key];
        }
        else {
            return null;
        }
    }

    /**
     * 添加一条错误信息
     * @param string $key
     * @param string $value
     * @return \tfc\validator\Validator
     */
    public function addError($key, $value)
    {
        if (isset($this->_errors[$key])) {
            if (!is_array($this->_errors[$key])) {
                $this->_errors[$key] = array($this->_errors[$key]);
            }

            $this->_errors[$key][] = $value;
        }
        else {
            $this->_errors[$key] = $value;
        }

        return $this;
    }

    /**
     * 通过键名判断错误信息是否存在
     * @param string|null $key
     * @return boolean
     */
    public function hasError($key = null)
    {
        if ($key === null) {
            return (count($this->_errors) > 0);
        }

        return isset($this->_errors[$key]);
    }
}
