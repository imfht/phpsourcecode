<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Adapter\Db;

use PDO;
use Exception;
use Ke\Adm\Adapter\DbAdapter;

abstract class PdoAbs implements DbAdapter
{


	/** @var null|string 数据源名称 */
	protected $source = '';

	/** @var array */
	protected $configuration = [];

	/** @var array 从服务器的配置信息 */
	protected $slaves = [];

	/** @var int 从服务器的数量 */
	protected $salveServerCount = 0;

	/** @var array 从服务器的查询计数器 */
	protected $salveQueryCounter = [];

	/** @var PDO */
	protected $connections = [];

	/** @var array PDO的设置 */
	protected $pdoOptions = [
		// 限制全部字段强制转换为小写
		PDO::ATTR_CASE              => PDO::CASE_NATURAL,
		// 空字符串转换为php的null值
		PDO::ATTR_ORACLE_NULLS      => PDO::NULL_EMPTY_STRING,
		// 数字内容转换为(true:强制转字符|false:源类型)类型
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false,
		//			PDO::ATTR_PERSISTENT		=> true
	];

	/** @var bool 默认自动提交为标准 */
	protected $isAutoCommit = true;

	protected $errorMode = PDO::ERRMODE_EXCEPTION;

	protected $operation = self::OPERATION_WRITE;

	/** @var \PDOStatement */
	protected $statement = null;

	public function __construct(string $source, array $config = null)
	{
		$this->source = $source;
		if (!empty($config))
			$this->configure($config);
	}

	public function getSourceName(): string
	{
		return $this->source;
	}

	public function configure(array $config)
	{
		$this->configuration = array_merge($this->configuration, $config);
		if (!empty($this->configuration['pdoOptions'])) {
			foreach ($this->configuration['pdoOptions'] as $key => $value)
				$this->pdoOptions[$key] = $value;
			unset($this->configuration['pdoOptions']);
		}
		if (!empty($this->configuration['slaves'])) {
			foreach ($this->configuration['slaves'] as $index => $slave) {
				if (empty($slave) || !is_array($slave) || empty($slave['host'])) {
					continue;
				}
				if (empty($slave['db']))
					unset($slave['db']);
				if (empty($slave['user']))
					unset($slave['user']);
				if (empty($slave['password']))
					unset($slave['password']);
				$slave['index'] = count($this->slaves);
				$this->slaves[] = $slave;
			}
			$this->salveServerCount = count($this->slaves);
			unset($this->configuration['slaves']);
		}
		return $this;
	}

	public function getConfiguration(): array
	{
		$clone = $this->configuration;
		$clone['slaves'] = $this->slaves;
		$clone['pdoOptions'] = $this->pdoOptions;
		return $clone;
	}

	public function getDatabase()
	{
		return $this->configuration['db'];
	}

	protected function switchMasterSlaveIndex($operation = self::OPERATION_WRITE): int
	{
		if ($this->salveServerCount <= 0 || $operation === self::OPERATION_WRITE) {
			return -1;
		} else {
			// 保留，一次请求，不连接超过2个数据库，主、从，各一
//			if (!isset($this->lastSlavesIndex))
//				$this->lastSlavesIndex = mt_rand(0, $this->slavesCount);
			return mt_rand(0, $this->salveServerCount);
		}
	}

	public function getMasterSlaveConfiguration(int $index): array
	{
		if ($index === -1 || !isset($this->slaves[$index])) {
			return $this->configuration;
		}
		return $this->slaves[$index] + $this->configuration;
//		if ($index === -1 || !isset($this->configuration['readSplit'][$index]))
//			return $this->configuration;
//		$config = $this->configuration['readSplit'][$index];
//		if (empty($config['host']) || empty($config['user']))
//			throw new Exception("db {$this->source}: invalid db config in offset {$index}, undefined host or user field.");
//		if (empty($config['db']))
//			$config['db'] = $this->configuration['db'];
//		if (empty($config['password']))
//			$config['password'] = '';
//		return $config;
	}

	/**
	 * @return PDO
	 * @throws Exception
	 */
	protected function getPDO()
	{
		$index = $this->switchMasterSlaveIndex($this->operation);
		if (!isset($this->connections[$index])) {
			$this->connect($index);
		}
		return $this->connections[$index];
	}

	public function connect(int $index = null)
	{
		if (!isset($index))
			$index = $this->switchMasterSlaveIndex($this->operation);
		if (isset($this->connections[$index]))
			return $this;
		$config = $this->getMasterSlaveConfiguration($index);
		try {
			$dsn = $this->getDSN($config);
			$pdo = new PDO(
				$dsn,
				$config['user'],
				$config['password'],
				$this->pdoOptions);
//            if (App::isEnv(App::ENV_PRO))
//                $this->errorMode = PDO::ERRMODE_SILENT;
			$pdo->setAttribute(PDO::ATTR_ERRMODE, $this->errorMode);
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
			$this->connections[$index] = $pdo;
			$this->isAutoCommit = true; // 默认以自动提交为标准
			$this->onConnect($config);
		} catch (\Exception $ex) {
			if (KE_APP_ENV === KE_DEV)
				throw new Exception("db {$this->source}: db connect error: {$ex->getMessage()}");
			else
				throw new Exception("{$this->source} db connect error");
		}
		return $this;
	}

	public function disconnect()
	{

	}

	abstract protected function getDSN(array $config): string;

	abstract protected function onConnect(array $config);

	/**
	 * 判断当前驱动是否已经连接
	 *
	 * @param int $index
	 * @return bool
	 */
	public function isConnect(int $index = null): bool
	{
		if (!isset($index))
			$index = $this->switchMasterSlaveIndex($this->operation);
		return isset($this->connections[$index]);
	}

	/**
	 * 启动事务接口
	 *
	 * @return $this
	 */
	public function startTransaction(): bool
	{
		if ($this->isAutoCommit) {
			$this->getPDO()->beginTransaction();
			$this->isAutoCommit = false;
		}
		return $this->isAutoCommit;
	}

	/**
	 * 注意特定的数据库如pgsql，需要重载该方法，调用PDO::inTransaction()为准。
	 *
	 * @return bool 是否已经启动事务。
	 */
	public function inTransaction(): bool
	{
		return !$this->isAutoCommit;
	}

	/**
	 * 提交事务
	 *
	 * @return bool
	 */
	public function commit(): bool
	{
		if (!$this->isAutoCommit) {
			$return = $this->getPDO()->commit();
			$this->isAutoCommit = true;
			return $return;
		}
		return false;
	}

	/**
	 * 回滚事务
	 *
	 * @return bool
	 */
	public function rollBack(): bool
	{
		if (!$this->isAutoCommit) {
			$return = $this->getPDO()->rollBack();
			$this->isAutoCommit = true;
			return $return;
		}
		return false;
	}

	/**
	 * 调用数据库驱动引用字符串
	 *
	 * @param string $string
	 * @return string
	 */
	public function quote(string $string): string
	{
		return $this->getPDO()->quote($string);
	}

	/**
	 * 预备执行SQL函数，如果SQL存在异常，会在这里抛出错误。
	 *
	 * @param string $sql
	 * @param array  $args
	 * @return \PDOStatement
	 */
	protected function prepare($sql, array $args = null)
	{
		$this->statement = $this->getPDO()->prepare($sql);
		$this->statement->execute($args);
		return $this->statement;
	}

	public function execute($sql, array $args = null, $operation = self::OPERATION_WRITE): int
	{
		$this->setOperation($operation);
		$st = $this->prepare($sql, $args);
		return $st->rowCount();
	}

	public function lastInsertId($table = null)
	{
		$this->setOperation(self::OPERATION_WRITE);
		return $this->getPDO()->lastInsertId();
	}

	public function query($sql, array $args = null, $find = self::MULTI, $fetch = self::FETCH_ASSOC, $arg = null)
	{
		$this->setOperation(self::OPERATION_READ);
		$st = $this->prepare($sql, $args);
		if ($fetch === self::FETCH_COLUMN && isset($arg)) {
			$column = $arg;
			$columnCount = $st->columnCount();
			$columnIndex = -1;
			if (is_numeric($column)) {
				if ($column >= 0 && $column < $columnCount)
					$columnIndex = $column;
			} elseif (is_string($column) && !empty($column)) {
				if (!$this->isSupportColumnMeta())
					throw new Exception("The driver of source '{$this->source}' does not support column meta!");
				for ($i = 0; $i < $columnCount; $i++) {
					$columnMeta = $st->getColumnMeta($i);
					if ($columnMeta['name'] === $column) {
						$columnIndex = $i;
						break;
					}
				}
			}
			if ($columnIndex > -1) {
				if ($find === self::ONE) {
					return $st->fetchColumn($columnIndex);
				} else {
					return $st->fetchAll(PDO::FETCH_COLUMN, $columnIndex);
				}
			}
			return false;
		} elseif ($fetch === self::FETCH_CLASS && !empty($arg) && class_exists($arg)) {
			$st->setFetchMode(PDO::FETCH_CLASS, $arg, null);
			if ($find === self::ONE) {
				return $st->fetch();
			} else {
				return $st->fetchAll();
			}
		} else {
			$fetch = $fetch === self::FETCH_ASSOC ? PDO::FETCH_ASSOC : PDO::FETCH_NUM;
			if ($find === self::ONE) {
				return $st->fetch($fetch);
			} else {
				return $st->fetchAll($fetch);
			}
		}
	}

	public function setOperation($operation)
	{
		$this->operation = $operation;
		return $this;
	}
}