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
use Ke\App, Ke\Web\Web, Ke\Web\Controller;

class NewView extends ReflectionCommand
{

	protected static $commandName = 'newView';

	protected static $commandDescription = '';

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $name = '';

	protected $parse = ['controller' => '', 'action' => ''];

	/** @var Web */
	protected $web = null;

	protected $webContext = [];

	protected $app = null;

	protected $router = null;

	protected $controller = null;

	protected $class = null;

	protected $view = null;

	protected $dir = null;

	/** @var \ReflectionClass */
	protected $reflection = null;

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
		$this->app = App::getApp(); // 获取项目下的 App 的实例
		$this->initWeb();

		$this->router = $this->web->getRouter();
		$this->parse = $this->router->parseStr($this->name);
		if (empty($this->parse['controller']))
			$this->exit("Please specify {$this->ansi('controller', 'red|bold')}, the right format should `{$this->ansi('controller#action', 'cyan')}`!");
		if (empty($this->parse['action']))
			$this->exit("Please specify {$this->ansi('action', 'red|bold')}, the right format should `{$this->ansi('controller#action', 'cyan')}`!");
		$this->controller = $this->web->filterController($this->parse['controller']);
		$this->class = $this->web->makeControllerClass($this->controller);
		$this->view = $this->web->filterAction($this->parse['action']);

		$dirs = $this->web->component->getScopeDirs('view');
		if (!isset($dirs['appView']))
			throw new \Exception("Unknown view folder!");
		$this->dir = $dirs['appView'];

		if (is_file($this->getPath()))
			$this->exit("The view file `{$this->ansi($this->getPath(), 'red|bold')}` is existing!");
	}

	protected function onExecute($argv = null)
	{
		if (file_put_contents($this->getPath(true), $this->buildContent())) {
			$this->console->println("Add view `{$this->ansi($this->getPath(), 'green|bold')}` success!");
		} else {
			$this->console->println("Add view `{$this->ansi($this->getPath(), 'red|bold')}` fail, please try again.");
		}
	}

	public function getPath(bool $checkDir = false)
	{
		$path = $this->dir . DS . $this->controller . DS . $this->view . '.phtml';
		if ($checkDir) {
			$dir = dirname($path);
			if (!is_dir($dir))
				mkdir($dir, 0755, true);
		}
		return $path;
	}

	public function getTemplateFile()
	{
		return 'View.tp';
	}

	public function getTemplatePath(): string
	{
		$tpl = '/Templates/' . $this->getTemplateFile();
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
		$vars = $this->webContext + [
			'path'         => "{$this->controller}/{$this->view}",
			'class'        => $this->class,
		];
		return substitute($content, $vars);
	}
}