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
use Ke\Cli\Argv;
use Ke\Cli\ReflectionCommand;
use Ke\Web\Web;

class NewController extends ReflectionCommand
{

	protected static $commandName = 'newController';

	protected static $commandDescription = '';

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $name = '';

	protected $class = '';

	/**
	 * @var string
	 * @type string
	 * @field   e
	 */
	protected $extend = '';

	/**
	 * @var string
	 * @type array
	 * @field   a
	 */
	protected $actions = [];

	/** @var Web */
	protected $web = null;

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
		$this->name = $this->web->filterController($this->name);
		$this->class = $this->web->makeControllerClass($this->name);
	}

	protected function onExecute($argv = null)
	{
		$path = $this->getPath();
		if (is_file($path))
			$this->exit("The controller `{$this->ansi($this->class, 'red|bold')}` is existing!");

		$content = $this->buildClass($this->class);
		$dir = dirname($path);
		if (!is_dir($dir))
			mkdir($dir, 0755, true);
		if (file_put_contents($path, $content)) {
			$this->console->println("Add controller `{$this->ansi($this->class, 'green|bold')}` success!");
			$action = $this->web->getDefaultAction();
			$command = $this->console->seekCommand(Argv::new("new-action {$this->name}#{$action}"));
			$command->execute();
		}
		else {
			$this->console->println("Add controller `{$this->ansi($this->class, 'red|bold')}` fail, please try again.");
		}
	}

	public function getPath()
	{
		return App::getApp()->src("{$this->class}", 'php');
	}

	public function buildClass(string $class)
	{
		$tpl = __DIR__ . '/Templates/Controller.tp';
		$vars = [];
		list($vars['namespace'], $vars['class']) = parse_class($class);
		if (!empty($vars['namespace']))
			$vars['namespace'] = "namespace {$vars['namespace']};";
		if (empty($this->extend)) {
			$vars['extend'] = 'Controller';
			$vars['use'] = 'use Ke\Web\Controller;';
		}
		else {
			$vars['extend'] = $this->extend;
		}

		return substitute(file_get_contents($tpl), $vars);
	}
}