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
use Ke\Utils\Git;

class GitExport extends ReflectionCommand
{

	protected static $commandName = 'git_export';

	protected static $commandDescription = 'Create a new command!';

	/**
	 * @var string
	 * @type string
	 * @field   1
	 */
	protected $version = '';

	/** @var Git */
	protected $git = null;

	protected function onPrepare($argv = null)
	{
		$this->git = new Git();
		if (empty($this->version))
			$this->version = $this->git->getLastVersion();
	}

	protected function onExecute($argv = null)
	{
		$start = microtime();
		$root = $this->git->getRoot();
		$dir = $this->getExportDir($this->version);
		$files = $this->git->getChangeFiles($this->version);
		$total = 0;

		foreach ($files as $file) {
			$path = real_path("{$root}/$file");
			if ($path && $this->copy($path, "{$dir}/{$file}")) {
				$this->console->println("exporting file {$file} ...");
				$total++;
			}
		}
		$usedTime = round(diff_milli($start), 4);
		$this->console->println("There are {$total} files export to \"{$dir}\", used {$usedTime} ms!");
	}

	public function getExportDir(string $version, int $deep = 0)
	{
		$dir = App::getApp()->path('export', $version . ($deep > 0 ? '_' . $deep : ''));
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
			return $dir;
		}
		return $this->getExportDir($version, $deep + 1);
	}

	public function copy($source, $target)
	{
		$dir = dirname($target);
		if (!is_dir($dir))
			mkdir($dir, 0755, true);
		return copy($source, $target);
	}
}