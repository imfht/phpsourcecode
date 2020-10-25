<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Cli;

use Ke\Cli\Cmd\Help;
use Ke\Helper\AnsiColorHelper;
use Throwable;
use Exception;
use Ke\App;

class Console
{

	/** @var Console */
	private static $context = null;

	private static $defaultScopes = null;

	/** @var \Ke\App */
	private $app = null;

	/** @var Argv */
	private $argv = null;

	/** @var Writer */
	private $writer = null;

	private $aliasCommands = [
		'new' => 'add',
	];

	private $cwd = '';

	/** @var AnsiColorHelper */
	private $ansi;

	/**
	 * @param null $argv
	 *
	 * @return $this
	 */
	public static function getConsole($argv = null)
	{
		if (!isset(self::$context)) {
			self::$context = new static($argv);
		}
		return self::$context;
	}

	public function getGlobalCommandScopes(): array
	{
		$scopes = $this->getAppCommandScopes();
		$scopes['Ke\\Cli\\Cmd'] = __DIR__ . DS . 'Cmd';
		return $scopes;
	}

	public function getAppCommandScopes(): array
	{
		$scopes = [];
		if (!empty(KE_APP_NS))
			$scopes[KE_APP_NS . '\\Cmd'] = KE_APP_NS_PATH . DS . 'Cmd';
		$scopes['Cmd'] = App::getApp()->src('Cmd');
		return $scopes;
	}

	final public function __construct(Argv $argv = null)
	{
		// 绑定当前的默认的上下文环境实例
		if (!isset(self::$context))
			self::$context = $this;

		$this->app = App::getApp();
		if (!$this->app->isInit())
			$this->app->init();

		$this->app->getLoader()->loadHelper('string');
		$this->writer = new Writer();
		$this->cwd = real_path(getcwd());

		// 将错误和异常处理，从App中接管过来。
		register_shutdown_function(function () {
			$this->onExiting();
		});
		set_error_handler([$this, 'errorHandle']);
		set_exception_handler([$this, 'exceptionHandle']);
		if (isset($argv)) {
			$this->setArgv($argv);
		}
	}

	protected function onExiting()
	{
	}

	/**
	 * PHP错误的处理的接管函数
	 */
	public function errorHandle($err, $msg, $file, $line, $context)
	{
		$err = error_name($err);
		$time = date('Y-m-d H:i:s');
		$this->halt("[{$err}][{$time}] {$msg} ({$file}#{$line})");
	}

	/**
	 * @param Throwable $throw
	 */
	public function exceptionHandle(Throwable $throw)
	{
		$err = get_class($throw);
		$msg = $throw->getMessage();
		$time = date('Y-m-d H:i:s');
		$file = $throw->getFile();
		$line = $throw->getLine();
		$this->halt("[{$err}][{$time}] {$msg} ({$file}#{$line})");
	}

	public function setArgv(Argv $argv)
	{
		$this->argv = $argv;
		return $this;
	}

	public function getArgv()
	{
		if (!isset($this->argv))
			$this->argv = Argv::current();
		return $this->argv;
	}

	public function getCwd()
	{
		return $this->cwd;
	}

	/**
	 * @return AnsiColorHelper
	 */
	public function getAnsi()
	{
		if (!isset($this->ansi)) {
			$this->ansi = new AnsiColorHelper();
		}
		return $this->ansi;
	}

	public function print(...$args)
	{
		$this->writer->output($args);
		return $this;
	}

	public function println(...$args)
	{
		$this->writer->output($args, true);
		return $this;
	}

	public function printf($message, ...$args)
	{
		if (!empty($args))
			$message = sprintf($message, ...$args);
		$this->writer->output($message);
		return $this;
	}

	public function halt(...$args)
	{
		$this->writer->output($args, true);
		exit();
		return $this;
	}

	public function getAliasCommand(string $cmd)
	{
		$lower = strtolower($cmd);
		if (isset($this->aliasCommands[$lower]))
			return $this->aliasCommands[$lower];
		return $cmd;
	}

	/**
	 * @param Argv|null $argv
	 *
	 * @return Command
	 * @throws Exception
	 */
	public function seekCommand(Argv $argv = null)
	{
		if (!isset($argv))
			$argv = $this->getArgv();
		if (empty($argv[0])) {
			return new Help();
//			throw new Exception('No command specified. You can try `kephp help` to get more help.');
		}

		$cmd = $this->getAliasCommand($argv[0]);
		$class = null;
		$path = null;
		$scopes = $this->getGlobalCommandScopes();
		$commands = $this->makeCommands($cmd);
		foreach ($scopes as $ns => $dir) {
			foreach ($commands as $command) {
				$path = real_file($dir . DS . $command . '.php');
				if ($path !== false) {
					$class = str_replace('/', '\\', $command);
					if (!empty($ns))
						$class = $ns . '\\' . $class;
					break;
				}
			}
			if (!empty($class))
				break;
		}
		if (empty($class))
			throw new Exception("No command detected with \"{$argv[0]}\"!");
		if (!class_exists($class))
			throw new Exception("Undefined command class {$class}");
		if (!is_subclass_of($class, Command::class))
			throw new Exception("Command class {$class} doest not extend with " . Command::class . '!');
		/** @var Command $cmd */
		return new $class($argv);
	}

	public function makeCommands(string $command): array
	{
		if (empty(($command = trim($command, KE_PATH_NOISE))))
			return [];
		$base = str_replace(['\\', '-', '.',], ['/', '_', '_',], $command);

		$lower = strtolower($command);
		$lowerNoUnder = str_replace('_', '', $lower);
		$camelCase = preg_replace_callback('#([\-\_\/\.\\\\])([a-z])#', function ($matches) {
			if ($matches[1] === '/' || $matches[1] === '\\')
				return strtoupper($matches[0]);
			else
				return '_' . strtoupper($matches[2]);
		}, ucfirst($lower));
		$camelCaseNoUnder = str_replace('_', '', $camelCase);
		return [
			$camelCaseNoUnder => $camelCaseNoUnder,
			$camelCase        => $camelCase,
			$lowerNoUnder     => $lowerNoUnder,
			$lower            => $lower,
			$base             => $base,
		];
//		if (!isset($result[$lower]))
//			$result[$lower] = $lower;

//		if (!isset($result[$lowerNoUnder]))
//			$result[$lowerNoUnder] = $lowerNoUnder;
		// 这里暂时这么处理

//		if (!isset($result[$camelCase]))
//			$result[$camelCase] = $camelCase;

//		if (!isset($result[$camelCaseNoUnder]))
//			$result[$camelCaseNoUnder] = $camelCaseNoUnder;
//		return $result;
	}
}