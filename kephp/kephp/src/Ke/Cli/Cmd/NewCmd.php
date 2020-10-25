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


use Ke\Cli\ReflectionCommand;

class NewCmd extends ReflectionCommand
{


	protected static $commandName = 'new_cmd';

	protected static $commandDescription = 'Create a new command!';

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $name = '';

	/**
	 * @var bool
	 * @type single
	 * @default true
	 */
	protected $isRef = true;

	private $selected = -1;

	private $prepareClasses = [];

	private $preparePaths = [];

	private $prepareCommands = [];

	protected function prepareCommands()
	{
		$index = 0;
		$scopes = $this->console->getAppCommandScopes();
		$commands = $this->console->makeCommands($this->name);
		foreach ($scopes as $ns => $dir) {
			foreach ($commands as $command) {
				$path = $dir . DS . $command . '.php';
				if (!KE_IS_WIN)
					$path = str_replace('\\', '/', $path);
				$class = str_replace('/', '\\', $command);
				if (!empty($ns))
					$class = $ns . '\\' . $class;
				$this->preparePaths[] = $path;
				$this->prepareClasses[] = $class;
				$this->prepareCommands[] = $command;
				$this->console->println("[{$index}]", $class, "({$path})");
				$index++;
			}
		}
	}

	protected function onPrepare($argv = null)
	{
		$this->prepareCommands();
		while (true) {
			$this->console->print("Please choice class name(input the number):", '');
			$this->selected = intval(trim(fgets(STDIN)));
			if (isset($this->prepareClasses[$this->selected])) {
				break 1;
			}
		}
		$this->console->println(
			PHP_EOL,
			"Creating class {$this->prepareClasses[$this->selected]} ({$this->preparePaths[$this->selected]}) ...",
			implode(PHP_EOL, $this->createClass()));
	}

	protected function onExecute($argv = null)
	{
		// TODO: Implement onExecute() method.
	}

	protected function createClass()
	{
		$class = $this->prepareClasses[$this->selected];
		$path = $this->preparePaths[$this->selected];
		$cmd = $this->prepareCommands[$this->selected];
		$tplFile = $this->isRef ? 'ReflectionCommand.tp' : 'Command.tp';
		$tplPath = __DIR__ . '/Templates/' . $tplFile;

		if (is_file($path))
			return ['Lost!', "The file \"{$path}\" is exists!"];
		if (!is_file($tplPath))
			return ['Lost!', "The template file \"{$tplFile}\" cannot found!"];

		$dir = dirname($path);
		if (!is_dir($dir))
			mkdir($dir, 0755, true);

		$tplContent = file_get_contents($tplPath);

		list($ns, $cls) = parse_class($class);

		$vars = [
			'class'     => $cls,
			'command'   => $this->name,
			'namespace' => $ns,
			'datetime'  => date('Y-m-d H:i'),
		];
		if (file_put_contents($path, substitute($tplContent, $vars)))
			return ['Success!', 'Please type: "php ' . KE_SCRIPT_FILE . ' ' . $cmd . ' --help"'];

		return ['Lost!', "{$path} save error!"];
	}
}