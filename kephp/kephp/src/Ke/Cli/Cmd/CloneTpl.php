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

class CloneTpl extends ReflectionCommand
{

	protected static $commandName = 'clone_tpl';

	protected static $commandDescription = 'Clone template file into app!';

	protected $templates = [
		'View.tp',
		'Layout.tp',
		'Widget.tp',
	];

	/**
	 * @var bool
	 * @type bool
	 * @default false
	 * @field recover
	 * @shortcut r
	 */
	protected $isRecover = false;

	protected $scopes = [];

	protected $selected = 0;

	protected function onPrepare($argv = null)
	{
		$scopes = $this->console->getAppCommandScopes();
		$index = 0;
		foreach ($scopes as $ns => $path) {
			$path = convert_path_slash($path, KE_DS_UNIX);
			$this->scopes[$index] = $path;
			$this->console->println("[{$index}]", $ns, '->', $path);
			$index += 1;
		}
	}

	protected function onExecute($argv = null)
	{
		while (true) {
			$this->console->print("Please choice namespace scope (default is {$this->selected}):", '');
			$this->selected = intval(trim(fgets(STDIN)));
			if (isset($this->scopes[$this->selected])) {
				$this->cloneTemplates($this->scopes[$this->selected]);
				break 1;
			}
		}
	}

	public function cloneTemplates(string $baseDir)
	{
		foreach ($this->templates as $tpl) {
			$file = __DIR__ . '/Templates/' . $tpl;
			$target = $baseDir . '/Templates/' . $tpl;
			if (is_file($target) && !$this->isRecover) {
				$this->console->println('The template', $tpl, 'is existing!');
				continue;
			}
			if (file_put_contents(predir($target), file_get_contents($file))) {
				$this->console->println('The template', $tpl, 'clone success!');
			}
		}
	}
}