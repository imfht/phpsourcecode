<?php
/**
 * @package     App.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月3日
 */

namespace SlimCustom\Libs;

use Interop\Container\ContainerInterface;
use SlimCustom\Libs\Container\Container;
use SlimCustom\Libs\Console\Console;
use Slim\Exception\InvalidMethodException;

/**
 * App
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class App extends \Slim\App
{
    
    /**
     * Name
     * 
     * @var string
     */
    const NAME = 'SlimCustom';
    
    /**
     * Author
     * 
     * @var string
     */
    const AUTHOR = 'Jing';
    
    /**
     * Version
     * 
     * @var string
     */
    const VERSION = '2.0';
    
    /**
     * App实例
     * 
     * @var \SlimCustom\Libs\App
     */
    public static $instance;

    /**
     * 应用名称
     * 
     * @var string
     */
    protected $name;

    /**
     * 应用地址
     * 
     * @var string
     */
    protected $path;
    
    /**
     * 部署状态
     * 
     * @var string
     */
    protected $deploymentStatus;
    
    /**
     * Init Application
     * 
     * @param array $environment
     */
    public function __construct($environment = [])
    {
        class_alias(static::class, 'App');
        $this->setName($environment['name']);
        $this->setPath($environment['path']);
        static::$instance = $this;
    }
    
    /**
     * Run application
     *
     * This method traverses the application middleware stack and then sends the
     * resultant Response object to the HTTP client.
     *
     * @param bool|false $silent
     * @return ResponseInterface
     *
     * @throws Exception
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function run($silent = false)
    {
        // 引导
        $this->boot();
        // CGI,FASTCGI,WEBMODULE,ISAPI
        if (strpos(PHP_SAPI, 'cli') === false) {
            $response = $this->container->get('response');
            try {
                $response = $this->process($this->container->get('request'), $response);
            }
            catch (InvalidMethodException $e) {
                $response = $this->processInvalidMethod($e->getRequest(), $response);
            }
            if (! $silent) {
                $this->respond($response);
            }
            return $response;
        }
        // CLI
        else {
            // 异常捕获处理
            try {
                // 处理命令
                $console = Console::class;
                if(isset($_SERVER['argv'][1])) {
                    $application = $_SERVER['argv'][1];
                    $appDir = config("application.{$application}.path", dirname(dirname(__DIR__)) . "/{$application}");
                    $this->setName($application);
                    $this->setPath($appDir);
                    $appConsole = "\\{$application}\\Console\\Console";
                    if (class_exists($appConsole)) {
                        $console = $appConsole;
                    }
                }
                return static::single($console)->run();
            }
            catch (\Throwable $e) {
                Console::error("%r{$e}%n");
            }
            catch (\Exception $e) {
                Console::error("%r{$e}%n");
            }
        }
    }

    /**
     * 引导
     */
    public function boot()
    {
        // PHP错误处理
        set_error_handler(function ($error, $error_string, $filename, $line, $symbols) {
            if (($error & error_reporting()) === $error) {
                throw new \ErrorException($error_string, $error, $error, $filename, $line);
            }
            return true;
        });
        // PHP脚本运行结束处理
        register_shutdown_function(function () {
            if ($error = error_get_last()) {
                $e = new \ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
                return $this->respond($this->finalize($this->handlePhpError($e, request(), response())));
            }
        });
        // 未捕获异常处理
        set_exception_handler(function ($e) {
            if ($e instanceof \Exception) {
                return $this->respond($this->finalize($this->handleException($e, request(), response())));
            }
            if ($e instanceof \Error) {
                return $this->respond($this->finalize($this->handlePhpError($e, request(), response())));
            }
        });
        // Set up configs
        $configs =  require_once $this->framerPath() . '/config/configs.php';
        if (is_file($this->configPath() . 'configs.php')) {
            $appConfigs =  require_once $this->configPath() . 'configs.php';
            ($appConfigs !== true) ?: $appConfigs = [];
            $configs = array_merge($configs, $appConfigs);
        }
        $this->setContainer([
            'settings' => $configs,
        ]);
        // Require Helpers
        require_once $this->framerPath() . 'Libs/Helpers/Helpers.php';
        if (is_file($this->path() . 'Libs/Helpers/Helpers.php')) {
            require_once $this->path() . 'Libs/Helpers/Helpers.php';
        }
        // Set up dependencies
        require_once $this->framerPath() . 'bootstrap/dependencies.php';
        if (is_file($this->path() . 'bootstrap/dependencies.php')) {
            require_once $this->path() . 'bootstrap/dependencies.php';
        }
        // Register routes
        if (is_file($this->path() . 'routes/routes.php')) {
            require_once $this->path() . 'routes/routes.php';
        }
    }

    /**
     * 设置应用名称
     * 
     * @param string $name
     * @return \SlimCustom\Libs\App
     */
    public function setName($name)
    {
        $this->name = trim($name);
        return $this;
    }

    /**
     * 设置容器
     * 
     * @param array $container
     * @throws InvalidArgumentException
     * @return \SlimCustom\Libs\App
     */
    public function setContainer($container = [])
    {
        if (is_array($container)) {
            $container = new Container($container);
        }
        if (! $container instanceof ContainerInterface) {
            throw new InvalidArgumentException('Expected a ContainerInterface');
        }
        $this->container = $container;
        return $this;
    }

    /**
     * 设置应用地址
     * 
     * @param string $path
     * @return \SlimCustom\Libs\App
     */
    public function setPath($path)
    {
        $this->path = rtrim($path, '\/');
        $this->deploymentStatus();
        return $this;
    }

    /**
     * 获取应用名称
     * 
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * 获取框架地址
     * 
     * @return string
     */
    public function framerPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    }
    
    /**
     * 获取应用地址
     * 
     * @return string
     */
    public function path()
    {
        return $this->path . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取应用配置地址
     * 
     * @return string
     */
    public function configPath()
    {
        return $this->path . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取data目录地址
     * 
     * @return string
     */
    public function dataPath()
    {
        return $this->path . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取公开目录地址
     * 
     * @return string
     */
    public function publicPath()
    {
        return $this->path . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
    }
    
    /**
     * 获取部署状态
     * 
     * @param $path null
     * @return string
     */
    public function deploymentStatus($path = null)
    {
        return $this->deploymentStatus = is_dir($path ? $path : $this->path()) ? true : false;
    }
    
    /**
     * 单例
     *
     * @param string | array $class
     * @return object
     */
    public static function single($class)
    {
        $alias = array_flip(config('alias', []));
        if (is_array($class)) {
            return array_map(function ($item) use ($alias){
                $item = isset($alias[$item]) ? $alias[$item] : $item;
                if (static::$instance->getContainer()->has($item)) {
                    return static::$instance->getContainer()->get($item);
                }
                return static::$instance->getContainer()[$item] = new $item();
            }, $class);
        }
        else {
            $class = isset($alias[$class]) ? $alias[$class] : $class;
            if (static::$instance->getContainer()->has($class)) {
                return static::$instance->getContainer()->get($class);
            }
            return static::$instance->getContainer()[$class] = new $class();
        }
    }
    
    /**
     * 依赖注入
     *
     * @param string | array $class
     * @return mixed
     */
    public static function di($class)
    {
        $alias = array_flip(config('alias', []));
        if (is_array($class)) {
            return array_map(function ($item) {
                $item = isset($alias[$item]) ? $alias[$item] : $item;
                if (static::$instance->getContainer()->has($item)) {
                    return static::$instance->getContainer()->get($item);
                }
                return static::$instance->getContainer()->parseDependencies($item);
            }, $class);
        }
        else {
            $class = isset($alias[$class]) ? $alias[$class] : $class;
            if (static::$instance->getContainer()->has($class)) {
                return static::$instance->getContainer()->get($class);
            }
            return static::$instance->getContainer()->parseDependencies($class);
        }
    }
}