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


use Ke\Cli\Command;
use Ke\Cli\ReflectionCommand;

class Add extends ReflectionCommand
{

	protected static $commandName = 'add';

	protected static $commandDescription = "Add class or file in application.\nInclude: app|cmd|model|action|controller|view|widget|layout.";

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $name;

	protected $commands = [
		'model'      => NewModel::class,
		'command'    => NewCmd::class,
		'cmd'        => NewCmd::class,
		'controller' => NewController::class,
		'action'     => NewAction::class,
		'view'       => NewView::class,
		'widget'     => NewWidget::class,
		'layout'     => NewLayout::class,
		'app'        => NewApp::class,
	];

	/** @var Command */
	protected $command = null;

	protected function getTip()
	{
		return implode('|', array_keys($this->commands));
	}

	protected function onPrepare($argv = null)
	{
		if (!isset($this->commands[$this->name]))
			throw new \Exception("Unknown add name, it should in {$this->getTip()}.");
		$class = $this->commands[$this->name];
		$newArgv = (array)$argv;
		array_shift($newArgv);
		$this->command = new $class($newArgv);
	}

	protected function onExecute($argv = null)
	{
		$this->command->execute();
	}
}