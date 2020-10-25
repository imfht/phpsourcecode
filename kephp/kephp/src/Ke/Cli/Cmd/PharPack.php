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
use FilesystemIterator, Phar;

class PharPack extends ReflectionCommand
{

	protected static $commandName = 'phar_pack';

	protected static $commandDescription = '';

	/**
	 * 要打包的目录
	 *
	 * @var string
	 * @type dir
	 * @require true
	 * @field   1
	 */
	protected $dir = '';

	/**
	 * 压缩包名称
	 *
	 * @var string
	 * @type string
	 * @field   2
	 */
	protected $name = '';

	/**
	 * 输出的目录
	 *
	 * @var string
	 * @type dir
	 * @field   export
	 * @shortcut e
	 */
	protected $export = '';

	protected function onPrepare($argv = null)
	{
		if (empty($this->export))
			$this->export = getcwd();
		if (empty($this->name))
			$this->name = basename($this->dir);
		if (intval(ini_get('phar.readonly')) > 0)
			$this->console->halt('phar.readonly is "on" now, please set phar.readonly as Off');
	}

	protected function onExecute($argv = null)
	{
		$stub      = $this->dir . '/stub';
		$class     = static::class;
		$pathParam = FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME;
		try {
			$phar = new Phar(predir($this->getSavePath()), $pathParam, $this->getFileName());
			$phar->buildFromDirectory($this->dir, '/.php|.phtml|.inc|.tp|.txt|.json$/');
			$phar->addFromString('pack.txt', $class . " packed in " . date('Y-m-d H:i:s'));
			if (is_file($stub) && is_readable($stub))
				$phar->setStub(substitute(file_get_contents($stub), ['file' => $this->getFileName()]));
			$this->console->println('Pack "' .
			                        $this->getFileName() .
			                        '" success, file export to "' .
			                        $this->getSavePath() .
			                        '"!');
		}
		catch (\Throwable $thrown) {
			$this->console->halt($thrown->getMessage());
		}
	}

	public function getFileName()
	{
		return ext($this->name, 'phar');
	}

	public function getSavePath()
	{
		return $this->export . DS . $this->getFileName();
	}
}