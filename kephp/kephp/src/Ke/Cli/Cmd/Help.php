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

class Help extends ReflectionCommand
{

	protected static $commandName = 'help';

	protected static $commandDescription = 'kephp-cli helper';

	public function getCommands()
	{
		return [
			"new app|command|controller|view|widget|layout name" => 'create new app/command/controller/view/widget/layout/model',
			"server -l=listen -r=web_root" => 'start php embed web server',
			"git_export version_hash" => 'export git version\'s files',
			"phar_pack dir -e=export" => 'pack the source to a phar file',
			"scan_tables db_source" => 'scan database tables to generate/update the models',
		];
	}

	protected function onPrepare($argv = null)
	{
	}

	protected function onExecute($argv = null)
	{
		$versions = [
			$this->ansi('PHP ' . phpversion(), 'blue'),
			$this->ansi('kephp ' . KE_VER, 'cyan'),
		];

		$this->println(implode(', ', $versions));
		$this->println('');

		$this->println("usage: `{$this->ansi('kephp command args', 'yellow')}` or `{$this->ansi('php ke.php command args', 'yellow')}`");
		$this->println('');

		$this->println('kephp built-in commands: ');
		foreach ($this->getCommands() as $command => $tip) {
			$this->println("  {$this->ansi("{$command}", 'green')}");
		}

	}
}