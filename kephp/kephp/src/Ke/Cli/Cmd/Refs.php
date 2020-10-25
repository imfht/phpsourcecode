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
use Ke\Utils\References;

class Refs extends ReflectionCommand
{

	protected static $commandName = 'refs';

	protected static $commandDescription = '';

	private $tplLog = <<<LOG
<?php
// App refs log, last update at {datetime}.
return [
	'references' => {references},
	'assets' => [
{assets}
	],
];
LOG;

	private $tplAssetRow = "\t\t['{0}', '{1}',],";


	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $cmd = '';

	/**
	 * @var array
	 * @type array
	 * @field   2
	 */
	protected $names = [];

	/**
	 * @var bool
	 * @type bool
	 * @field   r
	 */
	protected $isRefresh = false;

	/** @var References */
	protected $refs = null;

	protected $log = [
		'references' => [
		],
		'assets'     => [
		],
	];

	protected function onPrepare($argv = null)
	{
		$this->refs = new References();
		$this->refs->loadFile(App::getApp()->config('references', 'php'));
		$this->loadLog();
	}

	protected function onExecute($argv = null)
	{
		if (method_exists($this, $this->cmd))
			$this->{$this->cmd}($argv);
		else
			throw new \Exception("Unknown command about refs!");
	}

	public function getLogPath()
	{
		return App::getApp()->tmp('references_log.php');
	}

	public function loadLog()
	{
		if (is_file($this->getLogPath())) {
			$this->log = import($this->getLogPath());
		}
		if (!is_array($this->log))
			$this->log = [];
		return $this;
	}

	public function writeLog()
	{
		$path = $this->getLogPath();
		predir($path);
		$log = $this->makeLogText();
		if (file_put_contents($path, $this->makeLogText())) {
			$this->console->println("Write references update log success!");
		}
		return $this;
	}

	public function getLibraryNames()
	{
		$libs = $this->refs->getLibraries();
		$keys = array_keys($libs);
		if (empty($this->names))
			return $keys;
		$names = [];
		foreach ($this->names as $name) {
			if (isset($libs[$name]))
				$names[] = $name;
		}
		return $names;
	}

	public function update()
	{
		$keys = $this->getLibraryNames();
		$total = 0;
		foreach ($keys as $key) {
			$name = $this->refs->getName($key);
			$this->console->print("Downloading {$name} ...");
			try {
				$status = $this->refs->download($key, $this->isRefresh);
				if ($status === true) {
					$total += 1;
					$this->log['references'][$name] = date('Y-m-d H:i:s');
					$this->console->println("Success!");
				}
				else
					$this->console->println("Existing!");
			}
			catch (\Throwable $thrown) {
				$this->console->println("Failure! An error occurred: " . $thrown->getMessage());
			}
			$this->log['assets'][] = $this->refs->getAssetData($key);
		}
		if ($total > 0)
			$this->writeLog();
	}

	public function makeLogText()
	{
		$assets = [];
		foreach ($this->log['assets'] as $row) {
			$assets[] = substitute($this->tplAssetRow, $row);
		}
		$data = [
			'references' => var_export($this->log['references'] ?? [], true),
			'assets'     => implode(PHP_EOL, $assets),
			'datetime'   => date('Y-m-d H:i:s'),
		];
		$log = substitute($this->tplLog, $data);
		return $log;
	}
}