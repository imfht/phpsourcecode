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

class NewApp extends ReflectionCommand
{

	const PATTERN_NAMESPACE = '/^[a-z]{1,}[a-z0-9_]+(\\\\[a-z]{1,}[a-z0-9_]+)?$/i';

	protected static $commandName = 'new_app';

	protected static $commandDescription = 'Create a new application!';

	protected $directories = [
		'type'     => 'dir',
		'children' => [
			'.gitignore'    => ['type' => 'file', 'tpl' => 'App/gitignore.tp', 'replace' => false],
			'.htaccess'     => ['type' => 'file', 'tpl' => 'App/htaccess.tp', 'replace' => false],
			'bootstrap.php' => ['type' => 'file', 'tpl' => 'App/bootstrap.tp'],
			'ke.php'        => ['type' => 'file', 'tpl' => 'App/kephp.tp'],
			'public'        => [
				'type'     => 'dir',
				'children' => [
					'.htaccess' => ['type' => 'file', 'tpl' => 'App/public_htaccess.tp', 'replace' => false],
					'index.php' => ['type' => 'file', 'tpl' => 'App/public_index.tp'],
					//					'vendor'    => ['type' => 'dir'],
					'js'        => [
						'type'     => 'dir',
						'children' => [
							'app.js' => ['type' => 'file', 'tpl' => 'App/js_app.tp'],
							'page'   => ['type' => 'dir',],
						],
					],
					'css'       => [
						'type' => 'dir',
						//						'children' => [
						//							'main.less' => ['type' => 'file', 'tpl' => 'App/less_main.tp'],
						//							'less'      => [
						//								'type'     => 'dir',
						//								'children' => [
						//									'common.less' => ['type' => 'file', 'tpl' => 'App/less_common.tp'],
						//								],
						//							],
						//						],
					],
					'img'       => ['type' => 'dir'],
				],
			],
			'src'           => [
				'type'   => 'dir',
				'handle' => 'getNewAppSrcDirectories',
			],
			'config'        => [
				'type'     => 'dir',
				'children' => [
					'common.php'      => ['type' => 'file', 'tpl' => 'App/config_common.tp'],
					'development.php' => ['type' => 'file', 'tpl' => 'App/config_development.tp'],
					'test.php'        => ['type' => 'file', 'tpl' => 'App/config_test.tp'],
					'production.php'  => ['type' => 'file', 'tpl' => 'App/config_production.tp'],
					'references.php'  => ['type' => 'file', 'tpl' => 'App/config_references.tp'],
					'routes.php'      => ['type' => 'file', 'tpl' => 'App/config_routes.tp'],
				],
			],
		],
	];

	/**
	 * @var string
	 * @type string
	 * @require true
	 * @field   1
	 */
	protected $appDir = '';

	/**
	 * @var App
	 */
	protected $thisApp = null;

	protected $context = [
		'kephpLibEntry' => '',
		'appNamespace'  => '',
	];

	protected $entryFile = '/Ke/App.php';

	/**
	 * @var string
	 * @type string
	 * @field    namespace
	 * @shortcut n
	 */
	protected $appNamespace = '';


	public function getAppDir()
	{
		return $this->appDir;
	}

	public function makeAppNamespaceFromDir(string $appDir)
	{
		$split = explode('/', $appDir);
		$last = $split[count($split) - 1];
		return path2class($last, true);
	}

	public function verifyAppNamespace(string $appNamespace)
	{
		if (preg_match(self::PATTERN_NAMESPACE, $appNamespace)) {
			return true;
		}
		return false;
	}

	protected function onPrepare($argv = null)
	{
		$this->thisApp = App::getApp();
		// 增加过滤
		$this->appDir = $this->filterAppDir($this->appDir);
		$root = $this->getNewAppDir();
		if (is_dir($root))
			throw new \Exception("Directory {$root} is existing!");
		$kephpEntry = relative_path($root, $this->thisApp->kephp());
		list($path, $phar) = split_phar($kephpEntry);
		if ($phar !== false) {
			$kephpEntry = "'phar://' . __DIR__ . '{$path}/{$phar}{$this->entryFile}'";
		} else {
			$kephpEntry = "__DIR__ . '{$path}{$this->entryFile}'";
		}
		$this->context['kephpLibEntry'] = $kephpEntry;
		if (empty($this->appNamespace))
			$this->appNamespace = $this->makeAppNamespaceFromDir($this->appDir);
		else
			$this->appNamespace = $this->filterAppNamespace($this->appNamespace);

		if (!$this->verifyAppNamespace($this->appNamespace))
			throw new \Exception("App namespace should match {$this->ansi(self::PATTERN_NAMESPACE, 'cyan')}");
		$this->context['appNamespace'] = trim($this->appNamespace, KE_PATH_NOISE);
	}

	protected function onExecute($argv = null)
	{
		$this->entry($this->getNewAppDir(), $this->directories, null);
		$this->println('');
		$this->println("The application {$this->ansi($this->appNamespace, 'blue|bold|underline')} is created!");
		$this->println('');
		$this->println("1. entry the directory {$this->ansi($this->getNewAppDir(), 'yellow')}");
		$this->println("2. input `{$this->ansi('composer init', 'green')}` and `{$this->ansi('composer require kephp/kephp', 'green')}` to init composer.");
		$this->println("3. input `{$this->ansi('kephp new controller index', 'green')}` or `{$this->ansi('php ke.php new controller index', 'green')}` to start your web app development.");
		$this->println("4. input `{$this->ansi('kephp server', 'green')}`");

		// 不再添加默认的 index controller
//		chdir($this->getNewAppDir());
//		passthru("php ke.php add controller index");
	}

	public function getAppParentDir()
	{
		return dirname($this->thisApp->root());
	}

	public function filterAppDir(string $appDir): string
	{
		$dir = str_replace('\\', '/', $appDir);
		$dir = trim($dir, ' \\/');
		$dir = preg_replace('#\/{1,}#', '/', $dir);
		return $dir;
	}

	public function filterAppNamespace(string $appNamespace): string
	{
		$ns = str_replace('/', '\\', $appNamespace);
		$ns = trim($ns, ' \\/');
		$ns = preg_replace('#\\{1,}#', '/', $ns);
		return $ns;
	}

	public function getNewAppDir()
	{
		$dir = str_replace('\\', '/', $this->appDir);
		$dir = preg_replace('#\/{1,}#', '/', $this->appDir);
		return getcwd() . DS . $dir;
	}

	public function entry(string $parent, array $data, $name = null)
	{
		if (!isset($data['type']))
			return $this;
		$path = $parent;
		if (!empty($name))
			$path .= DS . $name;
		if ($data['type'] === 'dir') {
			$this->createDir($path);
			if (!empty($data['children'])) {
				foreach ($data['children'] as $key => $item) {
					$this->entry($path, $item, $key);
				}
			}
		} elseif ($data['type'] === 'file') {
			$this->createFile($path, $data['tpl'], $data);
		}
		if (isset($data['handle']) && is_callable([$this, $data['handle']])) {
			$handleData = call_user_func([$this, $data['handle']], $path);
			$this->entry($path, $handleData, null);
		}

		return $this;
	}

	public function createDir($dir)
	{
		if (!is_dir($dir) && mkdir($dir, 0777, true)) {
			$this->console->println("create dir  {$this->ansi($dir, 'cyan')} {$this->ansi("success", 'green')}");
		}
//		else {
//			$this->console->println("create dir  {$this->ansi($dir, 'cyan')} {$this->ansi("fail", 'red')}");
//		}
	}

	public function createFile($file, string $tpl = null, array $options)
	{
		$this->console->print("create file {$this->ansi($file, 'cyan')}");
		$tpl = __DIR__ . '/Templates/' . $tpl;
		if (is_file($tpl)) {
			$tplContent = file_get_contents($tpl);
			if (!isset($options['replace']) || $options['replace'] !== false) {
				$context = $this->context;
				if (!empty($options['context']) && is_array($options['context']))
					$context += $options['context'];
				$tplContent = substitute($tplContent, $context);
			}

			if (file_put_contents(predir($file), $tplContent)) {
				$this->console->println($this->ansi("success", 'green'));
			} else {
				$this->console->println($this->ansi("fail", 'red'));
			}
		}
	}

	public function getNewAppSrcDirectories(string $parent)
	{
		$data = [
			'type'     => 'dir',
			'children' => [
				$this->context['appNamespace'] => [
					'type'     => 'dir',
					'children' => [
						'App.php'    => ['type' => 'file', 'tpl' => 'App/App.tp'],
						'Web.php'    => ['type' => 'file', 'tpl' => 'App/Web.tp'],
						'Model'      => ['type' => 'dir'],
						'Controller' => ['type' => 'dir'],
						'Component'  => ['type' => 'dir'],
						'View'       => ['type' => 'dir'],
					],
				],
			],
		];
		return $data;
	}
}