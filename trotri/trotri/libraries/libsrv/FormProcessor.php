<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libsrv;

use tfc\ap\ErrorException;
use tfc\validator\Validator;
use tfc\saf\Log;

/**
 * FormProcessor abstract class file
 * 表单数据处理基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FormProcessor.php 1 2013-03-29 16:48:06Z huan.song $
 * @package libsrv
 * @since 1.0
 */
abstract class FormProcessor
{
	/**
	 * @var string 操作类型：新增记录
	 */
	const OP_INSERT = 'INSERT';

	/**
	 * @var string 操作类型：编辑记录
	 */
	const OP_UPDATE = 'UPDATE';

	/**
	 * @var integer 寄存ID值
	 */
	public $id = 0;

	/**
	 * @var string 操作类型：新增记录或编辑记录
	 */
	protected $_opType = '';

	/**
	 * @var libsrv\AbstractService 寄存业务类实例
	 */
	protected $_object = null;

	/**
	 * @var tfc\saf\DbProxy 寄存数据库代理操作类
	 */
	protected $_dbProxy = null;

	/**
	 * @var array 寄存所有错误信息
	 */
	protected $_errors = array();

	/**
	 * @var array 寄存所有表单元素的值
	 */
	protected $_values = array();

	/**
	 * 构造方法：初始化模型类
	 * @param \libsrv\AbstractService $object
	 */
	public function __construct(AbstractService $object)
	{
		$this->_object = $object;
	}

	/**
	 * 执行表单数据验证操作
	 * @param string $opType
	 * @param array $params
	 * @param integer|array $id
	 * @return boolean
	 * @throws ErrorException 如果指定的操作类型不是INSERT或UPDATE，抛出异常
	 * @throws ErrorException 如果是UPDATE操作类型但是ID小于等于0，抛出异常
	 */
	public function run($opType, array $params, $id = 0)
	{
		$this->clearValues();
		$this->clearErrors();

		$this->_opType = strtoupper($opType);
		if (!defined('static::OP_' . $this->_opType)) {
			throw new ErrorException(sprintf(
				'FormProcessor op type "%s" must be INSERT or UPDATE', $this->_opType
			));
		}

		$this->id = Clean::positiveInteger($id);
		if ($this->isUpdate() && $this->id === false) {
			$isArr = is_array($id);
			Log::warning(sprintf(
				'FormProcessor op type is Update, "%s" "%s" must be greater than 0',
				($isArr ? 'IDs' : 'ID'), ($isArr ? serialize($id) : $id)
			));

			return false;
		}

		$params = $this->_cleanPreProcess($params);
		if ($params === false) {
			return false;
		}

		if ($this->_process($params)) {
			return $this->_cleanPostProcess();
		}

		return false;
	}

	/**
	 * 处理表单数据
	 * @param array $params
	 * @param integer $id
	 * @return boolean
	 */
	protected abstract function _process(array $params);

	/**
	 * 验证前清理数据，需要子类重写此方法
	 * @param array $params
	 * @return array
	 */
	protected function _cleanPreProcess(array $params)
	{
		return $params;
	}

	/**
	 * 验证后清理数据，需要子类重写此方法
	 * @return boolean
	 */
	protected function _cleanPostProcess()
	{
		return true;
	}

	/**
	 * 验证字段必须存在
	 * @param array $params
	 * @return boolean
	 * @throws ErrorException 如果字段名不是字符串类型，抛出异常
	 */
	public function required(array $params)
	{
		$num = func_num_args();
		if ($num < 2) {
			return true;
		}

		$args = func_get_args();
		unset($args[0]);

		$result = true;
		foreach ($args as $columnName) {
			if (!is_string($columnName)) {
				throw new ErrorException('FormProcessor invalid column name.');
			}

			if (!isset($params[$columnName])) {
				$this->addError($columnName, sprintf('the column name "%s" of attributes is undefined', $columnName));
				$result = false;
			}
		}

		return $result;
	}

	/**
	 * 执行多个验证类
	 * @param array $params
	 * @return void
	 * @throws ErrorException 如果字段名不是字符串类型，抛出异常
	 */
	public function isValids(array $params)
	{
		$num = func_num_args();
		if ($num < 2) {
			return true;
		}

		$args = func_get_args();
		unset($args[0]);

		foreach ($args as $columnName) {
			if (!is_string($columnName)) {
				throw new ErrorException('FormProcessor invalid column name.');
			}

			if (!isset($params[$columnName])) {
				continue;
			}

			$value = $params[$columnName];

			$method = 'get' . str_replace('_', '', $columnName) . 'Rule';
			if (method_exists($this, $method)) {
				$validators = $this->$method($value);
			}
			else {
				$validators = array();
			}

			$this->isValid($columnName, $value, $validators);
		}
	}

	/**
	 * 执行指定的验证类
	 * @param string $columnName
	 * @param mixed $value
	 * @param array $validators
	 * @return boolean
	 * @throws ErrorException 如果字段名为空，抛出异常
	 * @throws ErrorException 如果验证类为空，抛出异常
	 * @throws ErrorException 如果验证类不是tfc\validator\Validator类的子类，抛出异常
	 */
	public function isValid($columnName, $value, array $validators)
	{
		if (($columnName = trim($columnName)) === '') {
			throw new ErrorException('FormProcessor the column name is undefined.');
		}

		if ($validators === null) {
			throw new ErrorException('FormProcessor the validators must be array.');
		}

		foreach ($validators as $className => $instance) {
			if (!$instance instanceof Validator) {
	            throw new ErrorException(sprintf(
	                'FormProcessor Validator class "%s" is not instanceof tfc\validator\Validator.', $className
	            ));
	        }

			if (!$instance->isValid()) {
				$this->addError($columnName, $instance->getMessage());
				return false;
			}
		}

		$this->$columnName = $value;
		return true;
	}

	/**
	 * 基于配置清理表单提交的数据
	 * <pre>
	 * 一.清理规则：
	 * $rules = array(
	 *	 'user_loginname' => 'trim',
	 *	 'user_interest' => array($foo, 'explode')
	 * );
	 * 参数：
	 * $attributes = array(
	 *	 'user_loginname' => '  abcdefghi  ',
	 *	 'user_interest' => ' 1, 2'
	 * );
	 * 结果：
	 * $result = array(
	 *	 'user_loginname' => 'abcdefghi',
	 *	 'user_interest' => array(1, 2)
	 * );
	 *
	 * 二.清理规则：
	 * $rules = array(
	 *	 'user_password' => 'md5',
	 *	 'user_interest' => array($foo, 'implode')
	 * );
	 * 参数：
	 * $attributes = array(
	 *	 'user_password' => '  1234  ',
	 *	 'user_interest' => array(1, 2)
	 * );
	 * 结果：
	 * $result = array(
	 *	 'user_loginname' => '81dc9bdb52d04dc20036dbd8313ed055',
	 *	 'user_interest' => '1,2'
	 * );
	 * </pre>
	 * @param array $rules
	 * @param array $attributes
	 * @return array
	 */
	public function clean(array $rules, array $attributes)
	{
		return Clean::rules($rules, $attributes);
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
	 * @return \libsrv\FormProcessor
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
	 * @return \libsrv\FormProcessor
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

	/**
	 * 获取所有的表单元素
	 * @return array
	 */
	public function getValues()
	{
		return $this->_values;
	}

	/**
	 * 清除所有的表单元素
	 * @return \libsrv\FormProcessor
	 */
	public function clearValues()
	{
		$this->_values = array();
		return $this;
	}

	/**
	 * 魔术方法：请求get开头的方法，获取一个表单元素的值
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return isset($this->_values[$name]) ? $this->_values[$name] : null;
	}

	/**
	 * 魔术方法：请求set开头的方法，设置一个表单元素的值
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->_values[$name] = $value;
	}

	/**
	 * 魔术方法：判断一个表单元素是否已经存在
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->_values[$name]);
	}

	/**
	 * 验证是否是“新增记录”操作类型
	 * @return boolean
	 */
	public function isInsert()
	{
		return $this->_opType === self::OP_INSERT;
	}

	/**
	 * 验证是否是“编辑记录”操作类型
	 * @return boolean
	 */
	public function isUpdate()
	{
		return $this->_opType === self::OP_UPDATE;
	}

	/**
	 * 获取数据库操作类
	 * @return \tfc\saf\DbProxy
	 */
	public function getDbProxy()
	{
		if ($this->_dbProxy === null) {
			$this->_dbProxy = $this->_object->getDb()->getDbProxy();
		}

		return $this->_dbProxy;
	}
}
