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

use Ke\Adm\Adapter\DbAdapter;
use Ke\Adm\Sql\Oracle\Forge;
use Ke\Adm\Sql\Oracle\QueryBuilder;
use PDO;

class PdoOracle extends PdoAbs
{

	protected $configuration = [
		'index'          => -1,
		'host'           => '127.0.0.1',
		'port'           => 1521,
		'db'             => '',
		'user'           => '',
		'password'       => '',
		'prefix'         => '',
		'charset'        => 'utf8',
		'slaves'         => [],
		'pdoOptions'     => [
			PDO::ATTR_CASE              => PDO::CASE_LOWER,
			// 空字符串转换为php的null值
			PDO::ATTR_ORACLE_NULLS      => PDO::NULL_TO_STRING,
			// 数字内容转换为(true:string|false:number)类型
//			PDO::ATTR_STRINGIFY_FETCHES => false,
//			PDO::ATTR_EMULATE_PREPARES  => true,
		],
		'datetimeFormat' => 'yyyy-mm-dd hh24:mi:ss',
		'mkSequence'     => 'T_%s_SEQ',
		'defaultPk'      => 'id',
		'defaultAutoInc' => true,
	];

	public function getDSN(array $config): string
	{
		$dsn = "oci:dbname=//{$config['host']}:{$config['port']}/{$config['db']};charset={$config['charset']}";
		return $dsn;
	}

	protected function onConnect(array $config)
	{
		if (!empty($config['datetimeFormat']))
			$this->getPDO()->query("alter session set nls_date_format = '{$config['datetimeFormat']}'");
		return $this;
	}

	public function getQueryBuilder()
	{
		if (!isset($this->queryBuilder))
			$this->queryBuilder = new QueryBuilder($this);
		return $this->queryBuilder;
	}

	public function getForge()
	{
		if (!isset($this->forge))
			$this->forge = new Forge($this);
		return $this->forge;
	}

	public function mkSequence(string $table)
	{
		$handle = $this->configuration['mkSequence'] ?? null;
		if (empty($handle))
			throw new \Exception("Invalid mkSequence value in '{$this->source}'!");
		if (is_string($handle)) {
			return sprintf($handle, $table);
		} elseif (is_callable($handle)) {
			return $handle($table);
		}
	}

	public function lastInsertId($table = null)
	{
		try {
			$seq = $this->mkSequence($table);
			return $this->query("SELECT {$seq}.CURRVAL FROM dual",
				null, DbAdapter::ONE, DbAdapter::FETCH_COLUMN, 0);
		} catch (\Throwable $throwable) {
			return null;
		}
	}

	public function isSupportColumnMeta(): bool
	{
		return false;
	}
}