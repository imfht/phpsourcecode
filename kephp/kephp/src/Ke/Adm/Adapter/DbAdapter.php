<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Adapter;

use Ke\Adm\Sql\ForgeImpl;
use Ke\Adm\Sql\MySQL\Forge;

interface DbAdapter
{

	const OPERATION_READ = 0;

	const OPERATION_WRITE = 1;

	const ONE = 0;

	const MULTI = 1;

	const FETCH_NUM = 0;

	const FETCH_ASSOC = 1;

	const FETCH_COLUMN = 2;

	const FETCH_CLASS = 3;

	/**
	 * DatabaseImpl constructor.
	 * @param string     $source
	 * @param array|null $config
	 */
	public function __construct(string $source, array $config = null);

	/**
	 * @param array $config
	 * @return mixed
	 */
	public function configure(array $config);

	/**
	 * @return array
	 */
	public function getConfiguration(): array;

	/**
	 * @return string
	 */
	public function getDatabase();

	public function getSourceName(): string;

	/**
	 * 是否已经连接
	 *
	 * @return bool
	 */
	public function isConnect(): bool;

	/**
	 * 连接数据库
	 *
	 * @return DbAdapter
	 */
	public function connect();

	public function disconnect();

	/**
	 * 启动事务
	 *
	 * @return bool
	 */
	public function startTransaction(): bool;

	/**
	 * 判断是否在事务中
	 *
	 * @return bool
	 */
	public function inTransaction(): bool;

	/**
	 * 提交事务
	 *
	 * @return bool
	 */
	public function commit(): bool;

	/**
	 * 回滚事务
	 *
	 * @return bool
	 */
	public function rollBack(): bool;

	public function quote(string $str): string;

	/**
	 * 执行一条 SQL 语句，并返回受影响的行数
	 *
	 * @param            $sql
	 * @param array|null $args
	 * @param int        $operation
	 * @return int
	 */
	public function execute($sql, array $args = null, $operation = self::OPERATION_WRITE): int;

	public function lastInsertId($table = null);

	/**
	 * @param            $sql
	 * @param array|null $args
	 * @param int        $find
	 * @param int        $fetch
	 * @param null       $arg
	 * @return mixed
	 */
	public function query($sql, array $args = null, $find = self::MULTI, $fetch = self::FETCH_ASSOC, $arg = null);

	public function getQueryBuilder();

	/**
	 * @return ForgeImpl|Forge
	 */
	public function getForge();

	public function setOperation($operation);

	public function isSupportColumnMeta(): bool;
}