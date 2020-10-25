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


use Ke\App;
use Ke\Cli\ReflectionCommand;

class Server extends ReflectionCommand
{

	const CURRENT_DIR = 1;

	const PARENT_DIR  = 2;

	protected static $commandName = 'server';

	protected static $commandDescription = 'start php embed web-server';

	/**
	 * @var string
	 * @type string
	 * @field    listen
	 * @shortcut l
	 */
	protected $listen = '';

	/**
	 * @var string
	 * @type string
	 * @field    documentRoot
	 * @shortcut d
	 */
	protected $documentRoot = '';

	/**
	 * @var string
	 * @type string
	 * @field    router
	 * @shortcut r
	 */
	protected $router = '';

	protected $cwd = '';

	protected $inKephpApp = 0;

	protected function onPrepare($argv = null)
	{
		if (empty($this->listen))
			$this->listen = 'localhost:8080';

		$this->cwd = getcwd();
		if (is_file("{$this->cwd}/ke.php"))
			$this->inKephpApp = 1;
		elseif (is_file("{$this->cwd}/../ke.php"))
			$this->inKephpApp = 2;

		if (empty($this->router)) {
			if ($this->inKephpApp) {
				$this->router = 'index.php';
			}
		}

		if (empty($this->documentRoot)) {
			if ($this->inKephpApp === self::CURRENT_DIR) {
				$this->documentRoot = $this->cwd . '/public';
			} elseif ($this->inKephpApp === self::PARENT_DIR) {
				$this->documentRoot = $this->cwd . '/../public';
			} else {
				$this->documentRoot = $this->console->getCwd();
				$dir = real_dir($this->documentRoot . '/public');
				if (!empty($dir) && is_dir($dir))
					$this->documentRoot = $dir;
			}
		} else {
			$dir = real_dir($this->documentRoot);
			if (empty($dir) || !is_dir($dir))
				$this->exit("Directory {$this->ansi($this->documentRoot, 'yellow')} {$this->ansi('not exists', 'red')}!");
			$this->documentRoot = $dir;
		}
		if (!empty($this->router)) {
			if (!is_file("{$this->documentRoot}/{$this->router}")) {
				$this->exit("The specified router file `{$this->ansi($this->router, 'red')}` does not exist!");
			}
		}
	}

	protected function onExecute($argv = null)
	{
		$versions = [
			$this->ansi('PHP ' . phpversion(), 'blue'),
			$this->ansi('kephp ' . KE_VER, 'cyan'),
		];

		$this->println(implode(', ', $versions));
		$dirTail = $this->inKephpApp ? '(kephp-app)' : '';
		$this->println("entry dir {$this->ansi($this->documentRoot, 'green')}{$dirTail}");
		chdir($this->documentRoot);

		if (empty($this->router)) {
			$this->println("Try to start php embed server in {$this->ansi("http://{$this->listen}", 'blue|underline')} without router");
			$this->println(system("php -S {$this->listen}"));
		} else {
			$this->println("Try to start php embed server in {$this->ansi("http://{$this->listen}", 'blue|underline')} with router {$this->ansi($this->router, 'yellow')}");
			$this->println(system("php -S {$this->listen} {$this->router}"));
		}
	}
}