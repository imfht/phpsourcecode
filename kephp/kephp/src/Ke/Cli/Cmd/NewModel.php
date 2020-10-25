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
use Ke\App;
use Ke\Cli\ReflectionCommand;

/**
 * Class NewModel
 * @package Cmd
 */
class NewModel extends ReflectionCommand
{

	protected static $commandName = 'newModel';

	protected static $commandDescription = '';

	// define fields: type|require|default|field|shortcut
	//         types: string|integer|double|bool|dir|file|realpath|json|concat|dirs|files|any...
	// enjoy coding everyday~~
	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $className = '';

	protected $namespace = '';

	/**
	 * @var string
	 * @type string
	 * @field t
	 */
	protected $tableName = '';

	/**
	 * @var string
	 * @type string
	 * @field s
	 */
	protected $source = null;

	/** @var DbAdapter */
	protected $adapter = null;

	protected $src = null;

	protected $tip = 'Create class';

	protected $isDebug = false;

	protected function onConstruct($argv = null)
	{
		$this->src = App::getApp()->src();
		$this->adapter = Db::getAdapter($this->source);

		if (empty($this->className))
			trigger_error('Please input a class name!', E_USER_ERROR);
		if (empty($this->tableName))
			trigger_error('Please input the table name!', E_USER_ERROR);

		$this->className = str_replace('/', '\\', $this->className);
//		list($this->namespace, $this->className) = parse_class($this->className);
	}

	protected function onPrepare($argv = null)
	{
		if (empty($this->tableName))
			trigger_error('Invalid table name, please specify table -t=<tableName>', E_USER_ERROR);
	}

	protected function onExecute($argv = null)
	{
//		$className = $this->getFullClassName();
		$this->console->print("{$this->tip} `{$this->ansi($this->className, 'cyan|bold')}` from table `{$this->ansi($this->tableName, 'yellow')}`");
		$message = $this->buildModel($this->tableName, $this->className, $this->getPath());
		$this->console->println(...$message);
	}

	public function setDebug(bool $isDebug) {
		$this->isDebug = $isDebug;
		return $this;
	}

	public function getPath()
	{
		return $this->src . DS . str_replace('\\', DS, $this->className) . '.php';
	}

	public function buildModel(string $table, string $class, string $path)
	{
		list($namespace, $pureClass) = parse_class($class);

		$tpl = $this->getTplContent();
		$forge = $this->adapter->getForge();
		$vars = $forge->buildTableProps($table);

		$vars['source'] = empty($this->source) ? "null" : "'{$this->source}'";
		$vars['namespace'] = $namespace;
		$vars['class'] = $pureClass;
		$vars['datetime'] = date('Y-m-d H:i:s');

		$content = substitute($tpl, $vars);

		if ($this->isDebug)
			return $content;

		if (is_file($path))
			return [$this->ansi('fail', 'red'), PHP_EOL, "File {$path} is existing!"];

		$dir = dirname($path);

		if (!is_dir($dir))
			mkdir($dir, 0755, true);

		if (file_put_contents($path, $content)) {
			return [$this->ansi('success', 'green')];
		} else {
			return [$this->ansi('fail', 'red'), PHP_EOL, 'I/O error, please try again!'];
		}
	}

	public function getTplContent()
	{
		return file_get_contents(__DIR__ . '/Templates/DbTableClass.tp');
	}

	public function getFullClassName()
	{
		$name = [];
		if (!empty(KE_APP_NS))
			$name[] = KE_APP_NS;
		$name[] = 'Model';
		if (!empty($this->namespace))
			$name[] = $this->namespace;
		$name[] = $this->className;
		return implode('\\', $name);
	}
}

