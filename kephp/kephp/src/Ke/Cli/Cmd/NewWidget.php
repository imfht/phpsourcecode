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
use Ke\Web\Web;

class NewWidget extends ReflectionCommand
{

	protected static $commandName = 'newWidget';

	protected static $commandDescription = '';

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $name = '';

	/** @var Web */
	protected $web = null;

	protected $webContext = [];

	protected $dir = null;

	protected $desc = 'widget';

	protected $template = 'Widget.tp';

	public function initWeb()
	{
		$class = implode('\\', [KE_APP_NS, 'Web']);
		if (class_exists($class) && is_subclass_of($class, Web::class)) {
			$this->web = $class::getWeb();
		} else {
			$this->web = Web::getWeb();
		}
		$this->webContext = [
			'webClass' => get_class($this->web),
			'htmlClass' => get_class($this->web->getHtml()),
			'httpClass' => get_class($this->web->http),
			'contextClass' => get_class($this->web->getContext()),
		];
	}

	protected function onPrepare($argv = null)
	{
		$this->initWeb();
		$dirs = $this->web->component->getScopeDirs('widget');
		if (!isset($dirs['appComponent']))
			throw new \Exception("Unknown layout folder!");
		$this->dir = $dirs['appComponent'];

		if (is_file($this->getPath()))
			throw  new \Exception("File {$this->getPath()} is existing!");
	}

	protected function onExecute($argv = null)
	{
		if (file_put_contents($this->getPath(true), $this->buildContent())) {
			$this->console->println("Add {$this->desc} '{$this->getPath()}' success!");
		} else {
			$this->console->println("Add {$this->desc} '{$this->getPath()}' lost, please try again.");
		}
	}

	public function getPath(bool $checkDir = false)
	{
		$path = $this->dir . DS . $this->name . '.phtml';
		if ($checkDir) {
			$dir = dirname($path);
			if (!is_dir($dir))
				mkdir($dir, 0755, true);
		}
		return $path;
	}

	public function getTemplatePath(): string
	{
		$tpl = '/Templates/' . $this->template;
		$scopes = $this->console->getAppCommandScopes();
		foreach ($scopes as $ns => $dir) {
			if (real_file($path = $dir . $tpl)) {
				return $path;
			}
		}
		return __DIR__ . $tpl;
	}

	public function buildContent(): string
	{
		$tpl = $this->getTemplatePath();
		$content = file_get_contents($tpl);
		return substitute($content, $this->webContext);
	}
}