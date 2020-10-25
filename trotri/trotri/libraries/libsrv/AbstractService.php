<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libsrv;

use tfc\ap\Application;
use tfc\ap\ErrorException;
use tfc\saf\Log;
use tdo\AbstractDb;

/**
 * AbstractService abstract class file
 * 业务层：模型基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: AbstractService.php 1 2013-05-18 14:58:59Z huan.song $
 * @package libsrv
 * @since 1.0
 */
abstract class AbstractService extends Application
{
	/**
	 * @var string 业务名
	 */
	protected $_srvName = '';

	/**
	 * @var string 模型类名
	 */
	protected $_className = '';

	/**
	 * @var libsrv\FormProcessor 表单数据处理类
	 */
	protected $_formProcessor = null;

	/**
	 * @var tdo\AbstractDb 数据库操作类
	 */
	protected $_db = null;

	/**
	 * 构造方法：初始化业务名和模型类名
	 */
	public function __construct()
	{
		list($this->_srvName, , $this->_className) = explode('\\', get_class($this));
	}

	/**
	 * 获取表单数据处理类
	 * @return \libsrv\FormProcessor
	 */
	public function getFormProcessor()
	{
		if ($this->_formProcessor === null) {
			$this->setFormProcessor();
		}

		return $this->_formProcessor;
	}

	/**
	 * 设置表单数据处理类
	 * @param \libsrv\FormProcessor $fp
	 * @return \tdo\AbstractDb
	 * @throws ErrorException 如果表单数据处理类类不存在，抛出异常
	 * @throws ErrorException 如果获取的实例不是libsrv\FormProcessor类的子类，抛出异常
	 */
	public function setFormProcessor(FormProcessor $fp = null)
	{
		if ($fp === null) {
			$className = $this->getSrvName() . '\\services\\Fp' . $this->getClassName();
			if (!class_exists($className)) {
				throw new ErrorException(sprintf(
					'AbstractService is unable to find the FormProcessor class "%s".', $className
				));
			}

			$fp = new $className($this);
			if (!$fp instanceof FormProcessor) {
				throw new ErrorException(sprintf(
					'AbstractService FormProcessor class "%s" is not instanceof libsrv\FormProcessor.', $className
				));
			}
		}

		$this->_formProcessor = $fp;
		return $this;
	}

	/**
	 * 获取所有的错误信息
	 * @param boolean $justOne
	 * @return array
	 */
	public function getErrors($justOne = true)
	{
		return $this->getFormProcessor()->getErrors($justOne);
	}

	/**
	 * 清理正整数数据，如果为负数则返回false
	 * @param integer|array $value
	 * @return mixed
	 */
	public function cleanPositiveInteger($value)
	{
		$result = Clean::positiveInteger($value);
		if ($result === false) {
			$isArr = is_array($value);
			Log::warning(sprintf(
				'AbstractService cleanPositiveInteger ARGS Error, "%s" "%s" must be greater than 0',
				($isArr ? 'PKs' : 'PK'), ($isArr ? serialize($value) : $value)
			));
		}

		return $result;
	}

	/**
	 * 通过主键，查询一条记录。不支持联合主键
	 * @param integer $value
	 * @return array
	 */
	public function findByPk($value)
	{
		if (($value = $this->cleanPositiveInteger($value)) === false) {
			return false;
		}

		$row = $this->getDb()->findByPk($value);
		return $row;
	}

	/**
	 * 通过主键，获取某个列的值。不支持联合主键
	 * @param string $columnName
	 * @param integer $value
	 * @return mixed
	 */
	public function getByPk($columnName, $value)
	{
		$row = $this->findByPk($value);
		if ($row && is_array($row) && isset($row[$columnName])) {
			return $row[$columnName];
		}

		return false;
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return integer
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$formProcessor = $this->getFormProcessor();
		if (!$formProcessor->run(FormProcessor::OP_INSERT, $params)) {
			return false;
		}

		$attributes = $formProcessor->getValues();
		$lastInsertId = $this->getDb()->create($attributes, $ignore);
		return $lastInsertId;
	}

	/**
	 * 通过主键，编辑一条记录。如果是联合主键，则参数是数组，且数组中值的顺序必须和PRIMARY KEY (pk1, pk2)中的顺序相同
	 * @param integer|array $value
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($value, array $params = array())
	{
		$formProcessor = $this->getFormProcessor();
		if (!$formProcessor->run(FormProcessor::OP_UPDATE, $params, $value)) {
			return false;
		}

		$attributes = $formProcessor->getValues();
		$rowCount = $this->getDb()->modifyByPk($formProcessor->id, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，删除一条记录。如果是联合主键，则参数是数组，且数组中值的顺序必须和PRIMARY KEY (pk1, pk2)中的顺序相同
	 * @param integer|array $value
	 * @return integer
	 */
	public function removeByPk($value)
	{
		if (($value = $this->cleanPositiveInteger($value)) === false) {
			return false;
		}

		$rowCount = $this->getDb()->removeByPk($value);
		return $rowCount;
	}

	/**
	 * 获取数据库操作类
	 * @return \tdo\AbstractDb
	 */
	public function getDb()
	{
		if ($this->_db === null) {
			$this->setDb();
		}

		return $this->_db;
	}

	/**
	 * 设置数据库操作类
	 * @param \tdo\AbstractDb $db
	 * @return \libsrv\AbstractService
	 * @throws ErrorException 如果DB类不存在，抛出异常
	 * @throws ErrorException 如果获取的实例不是tdo\AbstractDb类的子类，抛出异常
	 */
	public function setDb(AbstractDb $db = null)
	{
		if ($db === null) {
			$className = $this->getSrvName() . '\\db\\' . $this->getClassName();
			if (!class_exists($className)) {
				throw new ErrorException(sprintf(
					'AbstractService is unable to find the DB class "%s".', $className
				));
			}

			$db = new $className();
			if (!$db instanceof AbstractDb) {
				throw new ErrorException(sprintf(
					'AbstractService DB class "%s" is not instanceof tdo\AbstractDb.', $className
				));
			}
		}

		$this->_db = $db;
		return $this;
	}

	/**
	 * 获取业务名
	 * @return string
	 */
	public function getSrvName()
	{
		return $this->_srvName;
	}

	/**
	 * 获取模型类名
	 * @return string
	 */
	public function getClassName()
	{
		return $this->_className;
	}
}
