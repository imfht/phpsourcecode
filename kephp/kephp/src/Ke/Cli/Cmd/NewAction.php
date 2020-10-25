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


use Ke\Cli\Argv;
use Ke\Cli\ReflectionCommand;
use Ke\App, Ke\Web\Web, Ke\Web\Controller;

class NewAction extends ReflectionCommand
{

	protected static $commandName = 'newAction';

	protected static $commandDescription = '';

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $name = '';

	protected $classMethodName = '';

	/**
	 * @var bool
	 * @type bool
	 * @default true
	 * @field   v
	 */
	protected $addView = true;

	protected $parse = ['controller' => '', 'action' => ''];

	/** @var Web */
	protected $web = null;

	protected $router = null;

	protected $class = null;

	protected $method = null;

	/** @var \ReflectionClass */
	protected $reflection = null;

	public function initWeb()
	{
		$class = implode('\\', [KE_APP_NS, 'Web']);
		if (class_exists($class) && is_subclass_of($class, Web::class)) {
			$this->web = $class::getWeb();
		} else {
			$this->web = Web::getWeb();
		}
	}

	protected function onPrepare($argv = null)
	{
		$this->initWeb();
		$this->router = $this->web->getRouter();
		$this->parse = $this->router->parseStr($this->name);
		if (empty($this->parse['controller']))
			$this->exit("Please specify {$this->ansi('controller', 'red|bold')}, the right format should `{$this->ansi('controller#action', 'cyan')}`!");
		if (empty($this->parse['action']))
			$this->exit("Please specify {$this->ansi('action', 'red|bold')}, the right format should `{$this->ansi('controller#action', 'cyan')}`!");
		$this->class = $this->web->makeControllerClass($this->parse['controller']);
		$this->method = $this->web->filterAction($this->parse['action']);
		if (!class_exists($this->class, true))
			$this->exit("The class {$this->ansi($this->class, 'red|bold')} not found!");
		if (!is_subclass_of($this->class, Controller::class))
			$this->exit("The class {$this->ansi($this->class, 'red|bold')} it's not a controller class!");

		$this->reflection = new \ReflectionClass($this->class);

		if ($this->reflection->hasMethod($this->method))
			$this->exit("The method `{$this->ansi($this->method, 'red|bold')}` is defined in class `{$this->ansi($this->class, 'red|bold')}`!");
	}

	protected function onExecute($argv = null)
	{
		$rows = $this->buildAllFile();
		$content = implode('', $rows);
		if (file_put_contents($this->getPath(), $content)) {
			$this->console->println("Add method `{$this->ansi($this->name, 'green|bold')}` success!");
			if ($this->addView) {
				$command = $this->console->seekCommand(Argv::new("new-view {$this->name}"));
				$command->execute();
			}
		}
		else {
			$this->console->println("Add method `{$this->ansi($this->name, 'red|bold')}` fail, please try again.");
		}
	}

	public function getPath()
	{
		return App::getApp()->src("{$this->class}", 'php');
	}

	public function buildMethodBody(): string
	{
		$content = [
			"",
			"\tpublic function {$this->method}()",
			"\t{",
			"\t\t// return",
			"\t}",
		    "",
		];
		return implode(PHP_EOL, $content);
	}

	public function buildAllFile(): array
	{
		$path = $this->getPath();
		if (!is_file($path))
			throw new \Exception("The file {$path} not found!");
		if (!is_readable($path))
			throw new \Exception("The file {$path} not readable!");
		if (!is_writeable($path))
			throw new \Exception("The file {$path} not writable!");
		$endLine = $this->reflection->getEndLine();
		$insertLine = $endLine - 1;
		$handle = @fopen($path, 'r');
		$index = 0;
		$result = [];
		while (($buffer = fgets($handle, 4096)) !== false) {
			$index++;
			$result[] = $buffer;
			if ($index === $insertLine)
				$result[] = $this->buildMethodBody($this->method);
		}
		return $result;
	}
}