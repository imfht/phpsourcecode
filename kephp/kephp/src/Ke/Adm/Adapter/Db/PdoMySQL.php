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


use Ke\Adm\Sql\MySQL\Forge;
use Ke\Adm\Sql\QueryBuilder;

class PdoMySQL extends PdoAbs
{

	protected $configuration = [
		'index'      => -1,
		'host'       => '127.0.0.1',
		'port'       => 3306,
		'db'         => '',
		'user'       => '',
		'password'   => '',
		'prefix'     => '',
		'charset'    => 'utf8',
		'slaves'     => [],
		'pdoOptions' => [],
	];

	protected $queryBuilder = null;

	protected $forge = null;

	public function getDSN(array $config): string
	{
		$dsn = "mysql:dbname={$config['db']};host={$config['host']}";
		if (!empty($config['port']))
			$dsn .= ";port={$config['port']}";
		return $dsn;
	}

	protected function onConnect(array $config)
	{
		if (!empty($config['charset']))
			$this->getPDO()->query("set names '{$config['charset']}'");
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

	public function isSupportColumnMeta(): bool
	{
		return true;
	}

}