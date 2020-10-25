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

/*
const APP_MODEL_CLS_SEP = '';
const APP_MODEL_NS_SEP = '';
const APP_MODEL_NAMESPACES = [];
*/

use Ke\Adm\Adapter\DbAdapter;
use Ke\Adm\Db;
use Ke\Adm\Model;
use Ke\App;
use Ke\Cli\Argv;
use Ke\Cli\ReflectionCommand;

class SyncDbModel extends ReflectionCommand
{

	const TYPE_MODEL = 1;
	const TYPE_DB    = 0;

	const DEFAULT_CLS_SEP = '';

	const DEFAULT_NS_SEP = '\\';

	const SEP_ALLOW_VALUES = ['_', '', '\\'];

	const CUSTOM_COLUMNS_SPR = "//===== user define columns =====//";

	protected static $commandName = 'sync-db-model';

	protected static $commandDescription = '';

	/**
	 * @var string
	 * @type string
	 * @field   1
	 */
	protected $source = '';

	/** @var DbAdapter */
	protected $adapter = null;

	protected $src = '';

	protected $config = [];

	protected $namespace = '';

	protected $prefix = '';

	protected $tables = [];

	protected $clsSep = self::DEFAULT_CLS_SEP;

	protected $nsSep = self::DEFAULT_NS_SEP;

	protected $namespaces = [];

	protected $tplContent = null;

	protected $customColumnsSpr = self::CUSTOM_COLUMNS_SPR;

	protected $isDebug = false;

	public function prepareAppModelConfig()
	{
		if (defined('APP_MODEL_NAMESPACES')) {
			$this->setNamespaces(constant('APP_MODEL_NAMESPACES'));
		}
		if (defined('APP_MODEL_CLS_SEP')) {
			$this->setClassSep(constant('APP_MODEL_CLS_SEP'));
		}
		return $this;
	}

	public function getConstantNamespaceValue(string $name, string $defaultValue = '')
	{
		if (defined($name) && ($ns = trim(constant($name), ' \\/')) !== '') {
			return $ns;
		}
		return $defaultValue;
	}

	public function joinClassName(int $type = self::TYPE_DB, string ...$suffix)
	{
		$className = [];
		$appNs = $this->getConstantNamespaceValue('KE_APP_NS', '');
		if ($appNs !== '')
			$className[] = $appNs;
		$ns = '';
		if ($type === self::TYPE_DB) {
			$ns = $this->getConstantNamespaceValue('APP_DB_NS', 'Db');
		} else {
			$ns = $this->getConstantNamespaceValue('APP_DB_NS', 'Model');
		}
		if (!empty($ns))
			$className[] = $ns;
		if (!empty($suffix)) {
			foreach ($suffix as $item) {
				$item = trim($item, ' \\/');
				if (!empty($item))
					$className[] = $item;
			}
		}
		return implode($this->nsSep, $className);
	}

	public function setNamespaces($namespaces, bool $isMerge = true)
	{
		if (is_string($namespaces)) {
			$ns = [];
			foreach (preg_split('#[\,\|]+#', $namespaces) as $namespace) {
				$namespace = trim($namespace, ',| ');
				if (!empty($namespace)) {
					$ns[$namespace] = true;
				}
			}
			$namespaces = $ns;
		}
		if (!is_array($namespaces))
			$namespaces = [];
		if (!$isMerge || empty($this->namespaces)) {
			$this->namespaces = $namespaces;
		} else {
			$this->namespaces = array_merge($this->namespaces, $namespaces);
		}
		return $this;
	}

	public function getNamespaces()
	{
		return $this->namespaces;
	}

	public function setClassSep($sep)
	{
		if (!is_string($sep))
			$sep = self::DEFAULT_CLS_SEP;
		else {
			if (mb_strlen($sep) > 1)
				$sep = mb_substr($sep, 0, 1);
			if (array_search($sep, self::SEP_ALLOW_VALUES) === false)
				$sep = self::DEFAULT_CLS_SEP;
		}
		$this->clsSep = $sep;
		return $this;
	}

	public function getClassSep()
	{
		return $this->clsSep;
	}

	public function removePrefix(string $tableName): string
	{
		if (empty($this->prefix))
			return $tableName;
		$prefix = trim($this->prefix, ' _');
		if (stripos($tableName, "{$prefix}_") === 0) {
			return substr($tableName, strlen("{$prefix}_"));
		}
		return $tableName;
	}

	public function convertToClassName(string $name)
	{
		$name = mb_strtolower($name);
		$name = ucfirst($name);
		return $name;
	}

	public function setDebug(bool $isDebug)
	{
		$this->isDebug = $isDebug;
		return $this;
	}

	public function mkClassName(string $tableName)
	{
		$ns = $this->getNamespaces();
		$nsSep = $this->nsSep;
		$clsSep = $this->getClassSep();

		$tableName = $this->removePrefix($tableName);
		$tableName = preg_replace('/[_]+/', '_', $tableName);

		$segments = explode('_', $tableName);

		$dbClassName = [];

		$newSegments = [];

		$modelNamespace = '';
		$modelClassName = [];
		$prefixSegments = [];
		$namespaceSegments = [];
		$size = count($segments);

		$isHitNs = false;

		foreach ($segments as $index => $segment) {
			if (empty($segment)) continue;
			$clsName = $this->convertToClassName($segment);
			$dbClassName[] = $clsName;
			$newSegments[] = $clsName;
			$prefixSegments[] = $segment;
			$namespaceSegments[] = $clsName;
			$prefix = implode('_', $prefixSegments);
			$nsPrefix = implode($nsSep, $namespaceSegments);
			// 是否提供一个 $nsPrefix 的一个空间？
			if (!empty($ns[$prefix])) {
				if (is_string($ns[$prefix]))
					$modelNamespace = $ns[$prefix];
				else
					$modelNamespace = $nsPrefix;
				$isHitNs = true;
				continue;
			}
			if ($isHitNs)
				$modelClassName[] = $clsName;
		}
		$dbClassName = $this->joinClassName(self::TYPE_DB, implode($clsSep, $dbClassName));

		if (!$isHitNs) {
			$modelClassName = $newSegments;
		}
		if (empty($modelClassName)) {
			$modelClassName = [$newSegments[$size - 1]];
		}
		$modelClassName = $this->joinClassName(self::TYPE_MODEL, $modelNamespace, implode($clsSep, $modelClassName));

		return ['db' => $dbClassName, 'model' => $modelClassName];
	}

	protected function onPrepare($argv = null)
	{
		$this->src = App::getApp()->src();
		$this->adapter = Db::getAdapter($this->source);
		$this->config = $this->adapter->getConfiguration();
		$this->prefix = trim($this->config['prefix'] ?? '', ' -_.');
		//
		$this->tables = $this->adapter->getForge()->getDbTables();

		$this->prepareAppModelConfig();
	}

	protected function onExecute($argv = null)
	{
		$start = microtime();
		$total = 0;

		foreach ($this->tables as $table) {
			$className = $this->mkClassName($table['name']);

			if (class_exists($className['db'])) {
				$command = $this->console->seekCommand(Argv::new("update-model {$className['db']} -t={$table['name']} -s={$this->source}"));
			} else {
				$command = $this->console->seekCommand(Argv::new("new-model {$className['db']} -t={$table['name']} -s={$this->source}"));
			}
			$command->execute();

			$this->generateOrUpdateModel($className['model'], $className['db']);
			$total++;
		}
		$usedTime = round(diff_milli($start), 4);
		$this->console->println("There are {$this->ansi($total, 'purple')} model create or update, used {$this->ansi($usedTime, 'yellow')} ms!");
	}

	public function makeClassPath($className)
	{
		return $this->src . DS . str_replace('\\', DS, $className) . '.php';
	}

	public function generateOrUpdateModel($modelClass, $dbClass)
	{
		if (!class_exists($dbClass, false)) {
			$path = App::getApp()->getLoader()->seek(null, $dbClass, false, true);
			if ($path === false)
				throw new \Exception('Could not import class ' . $dbClass);
			require_once $path;
		}
		list($modelNamespace, $modelPureClass) = parse_class($modelClass);
		$dbClass = '\\' . trim($dbClass, ' \\/');

		$dbColumns = $dbClass::dbColumns();
		$rawColumns = $this->generateColumns($dbColumns, $modelClass);
		$columns = implode(",\r\n", $rawColumns);
		$userColumns = "{$this->customColumnsSpr}
	protected static \$columns = [
{$columns}\t];
	{$this->customColumnsSpr}";
		$path = '';
		$content = '';

		$isGenerate = false;

		if (!class_exists($modelClass)) {
			$isGenerate = true;
			$vars = [
				'namespace'      => $modelNamespace,
				'modelClassName' => $modelPureClass,
				'dbClassName'    => $dbClass,
				'userColumns'    => $userColumns,
			];

			$content = substitute($this->getTplContent(), $vars);
		} else {
			// 已经存在的 Model 暂时不做处理
			$isGenerate = false;
			$ref = new \ReflectionClass($modelClass);
			$path = $ref->getFileName();
			$modelClassContent = file_get_contents($path);
			$split = preg_split("#\/[\*]+\s+user\s+define\s+columns\s+[\*]+\/#", $modelClassContent);
			$content = $split[0] . $userColumns . $split[2];
		}

		if (empty($content)) {
			throw new \Exception('生成 Model 内容为空！');
		}

		if ($this->isDebug) {
			return $content;
		}

		if (!$isGenerate)
			return false;

		if (empty($path)) {
			$path = $this->makeClassPath($modelClass);
			predir($path);
		}

		if (file_put_contents($path, $content)) {
			$this->println("Generate model `{$this->ansi($modelClass, 'cyan|bold')}` {$this->ansi('success', 'green')}!");
			return true;
		} else {
			$this->println("Generate model `{$this->ansi($modelClass, 'cyan|bold')}` {$this->ansi('fail', 'red')}!");
			return false;
		}
	}

	public function generateColumns(array $dbColumns, $modelClass)
	{
		$userColumns = [];
		if (class_exists($modelClass)) {
			$ref = new \ReflectionClass($modelClass);
			$staticProps = $ref->getStaticProperties();
			if (!empty($staticProps['columns'])) {
				$userColumns = $staticProps['columns'];
				// 补完一下 userColumns 缺失的字段
				foreach ($dbColumns as $key => $column) {
					if (!isset($userColumns[$key])) {
						$userColumns[$key] = [
							'showTable' => false,
						];
					}
				}
			}
		} else {
			foreach ($dbColumns as $key => $column) {
				$userColumns[$key] = [];
			}
		}
		$keys = array_keys($userColumns);
		$keyMaxLength = 0;
		$result = [];
		foreach ($keys as $key) {
			$length = strlen($key);
			if ($keyMaxLength <= 0 || $length > $keyMaxLength)
				$keyMaxLength = $length;
		}

		foreach ($keys as $key) {
			$k = "'{$key}'";
			$k = str_pad($k, $keyMaxLength + 2, ' ');
			$column = $userColumns[$key] ?? [];
			$columnContent = empty($column) ? '[]' : $this->varexport($userColumns[$key] ?? [], "\t\t");
			$result[] = "\t\t{$k} => " . $columnContent;
		}

		if (count($result) > 0)
			$result[] = '';
		return $result;
	}

	public function varexport($expression, string $indent = "")
	{
		$export = var_export($expression, true);
//		$export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
		$array = preg_split("/\r\n|\n|\r/", $export);
		$array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
		$array = array_filter(["["] + $array);
		$arraySize = count($array);
		$type = 'inline';
		if ($type === 'block') {
			foreach ($array as $index => &$item) {
				if ($index === 0) continue;
				if (preg_match("#^(\s{2,})(.*)$#", $item, $match)) {
					$size = strlen($match[1]);
					$count = intval($size / 2);
					$prefix = $indent . str_repeat("\t", $count);
					$item = $prefix . $match[2];
				} else {
					$item = $indent . $item;
				}
			}
			$export = join(PHP_EOL, $array);
		} else {
			foreach ($array as $index => &$item) {
				if ($index === 0) continue;
				if (preg_match("#^(\s{2,})(.*)$#", $item, $match)) {
					$item = $match[2];
				} else {
				}
			}
			$export = join('', $array);
		}

		return $export;
	}

	public function generateNewModel($modelClass, $dbClass)
	{
		$path = $this->makeClassPath($modelClass);
		list($modelNamespace, $modelPureClass) = parse_class($modelClass);
		$dbClass = '\\' . trim($dbClass, ' \\/');

		$vars = [
			'namespace'      => $modelNamespace,
			'modelClassName' => $modelPureClass,
			'dbClassName'    => $dbClass,
		];

		predir($path);

		if (file_put_contents($path, substitute($this->getTplContent(), $vars))) {
			$this->println("Generate model `{$this->ansi($modelClass, 'cyan|bold')}` {$this->ansi('success', 'green')}!");
		} else {
			$this->println("Generate model `{$this->ansi($modelClass, 'cyan|bold')}` {$this->ansi('fail', 'red')}!");
		}
	}

	public function getTplContent()
	{
		if (empty($this->tplContent)) {
			$this->tplContent = file_get_contents(__DIR__ . '/Templates/DbModelClass.tp');
		}
		return $this->tplContent;
	}

}