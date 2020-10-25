<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Cli\Cmd;


use Ke\Adm\Adapter\DbAdapter;
use Ke\Adm\Db;
use Ke\Cli\ReflectionCommand;

class ScanTables extends ReflectionCommand
{

	protected static $commandName = 'scan_tables';

	protected static $commandDescription = '';

	/**
	 * @var string
	 * @type string
	 * @field   1
	 */
	protected $source = '';

	/** @var DbAdapter */
	protected $adapter = null;

	protected $config = [];

	/**
	 * @var string
	 * @type string
	 * @field    namespace
	 * @shortcut n
	 */
	protected $namespace = '';

	protected $prefix = '';

	protected $tables = [];

	protected function onPrepare($argv = null)
	{
		$this->adapter = Db::getAdapter($this->source);
		$this->config = $this->adapter->getConfiguration();
		$this->prefix = trim($this->config['prefix'] ?? '', ' -_.');
		//
		$this->tables = $this->adapter->getForge()->getDbTables();

		if (!empty($this->namespace)) {
			$this->namespace = str_replace('/', '\\', trim($this->namespace, KE_PATH_NOISE));
		}
	}

	protected function onExecute($argv = null)
	{
		$start = microtime();
		$total = 0;

		foreach ($this->tables as $table) {
			$className = $this->mkClassName($table['name']);
			if (class_exists($className)) {
				$command = new UpdateModel(['', $className, 't' => $table['name'], 's' => $this->source]);
			} else {
				$command = new NewModel(['', $className, 't' => $table['name'], 's' => $this->source]);
			}
			$command->execute();
//			$class = $this->parseTableNameInGroup($table['name']);
//			$command = new NewModel(['', $class]);
//			if (is_file($command->getPath()))
//				$command = new UpdateModel(['', $class]);
//			$command->execute();
			$total++;
		}
		$usedTime = round(diff_milli($start), 4);
		$this->console->println("There are {$total} model create or update, used {$usedTime} ms!");
	}

	public function removePrefix(string $tableName): string
	{
		if (empty($this->prefix))
			return $tableName;
		if (stripos($tableName, "{$this->prefix}_") === 0) {
			return substr($tableName, strlen("{$this->prefix}_"));
		}
		return $tableName;
	}

	public function mkClassName(string $tableName)
	{
		$fullName = [];
		if (!empty(KE_APP_NS))
			$fullName[] = KE_APP_NS;

		$modelNS = defined('KE_DB_NS') ? trim(constant('KE_DB_NS'), ' \\/') : 'Db';
		if (!empty($modelNS))
			$fullName[] = $modelNS;

		$tableName = preg_replace('/[_]+/', '_', $tableName);
		$segments = explode('_', $tableName);
		$className = [];
		if (count($segments) > 1) {
			$first = array_shift($segments);
			$first = mb_strtolower($first);
			$first = ucfirst($first);
			$fullName[] = $first;
			foreach ($segments as $segment) {
				if (empty($segment))
					continue;
				$segment = mb_strtolower($segment);
				$segment = ucfirst($segment);
				$className[] = $segment;
			}
			$className = implode('_', $className);
		} else {
			$className = mb_strtolower($tableName);
			$className = ucfirst($className);
		}
		$fullName[] = $className;

		return implode('\\', $fullName);
	}

	public function parseTableNameInGroup(string $tableName)
	{
		$name = $this->removePrefix($tableName);
		$parse = explode('_', $name);
		$namespace = ucfirst($parse[0]);
		$class = str_replace(' ', '_', ucwords(str_replace('_', ' ', $name)));
		if (!empty($this->namespace))
			$namespace .= '\\' . $this->namespace;
		return $namespace . '\\' . $class;
	}
}