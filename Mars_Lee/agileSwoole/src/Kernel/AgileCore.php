<?php


namespace Kernel;

use Component\Orm\Connection\Mongodb;
use Component\Orm\Connection\Mysql;
use Component\Orm\Pool\ConnectionPool;
use Component\Orm\Pool\PoolFactory;
use Kernel\Core\Conf\Config;
use Kernel\Core\Di\Container;
use Kernel\Core\Di\IContainer;

class AgileCore
{
    /* @var AgileCore $core */
    public static $core = null;
    /* @var \Kernel\Core\Conf\Config $core */
    public static $config = null;
    protected $container;
    protected $reflection;

    protected $workerClassMap = [
        'pool' => PoolFactory::class
    ];

    /**
     * 核心类构造
     * AgileCore constructor.
     * @param array $paths
     * @param array $confPath
     * @throws \Exception
     */
    private function __construct(array $paths = [], array $confPath = [])
    {
        if(!defined('APP_PATH')) {
            define('APP_PATH', $paths[0]);
        }
        $this->autoload($paths);
        $this->container = new Container();
        $this->container->bind('container', $this->container);
        /** @var Config $config */
        self::$config = $config = $this->container->bind('config', Config::class)->get('config');
        $config->setLoadPath($confPath);
        $this->container->alias('Psr\Container\ContainerInterface', $this->container);
    }

    /**
     * @param string $driver
     *
     * @throws \Exception
     */
    public static function setConfigDriver(string $driver) {
        if(!(self::$core instanceof AgileCore)) {
            throw new \Exception('please init core first!');
        }

        self::$config->setDriverType($driver);
    }
    /**
     * 啊洗吧 英文不好 做兼容
     * @return \Kernel\AgileCore
     * @throws \Exception
     */
    public static function getInstant()
    {
        return self::getInstance();
    }
    /**
     * @return \Kernel\AgileCore
     * @throws \Exception
     */
    public static function getInstance()
    {
        if(!(self::$core instanceof AgileCore)) {
            throw new \Exception('please init core first!');
        }

        return self::$core;
    }
    /**
     * 获取Core对象
     *
     * @param array $paths
     * @param array $confPath
     *
     * @return AgileCore
     * @throws \Exception
     */
    public static function init(array $paths = [], array $confPath = []) : AgileCore
    {
        if(!(self::$core instanceof AgileCore)) {
            self::$core = new self($paths, $confPath);
        }

        return self::$core;
    }

    /**
     * 注册加载SRC下文件
     * @param array $paths
     */
    public function autoload(array $paths = [])
    {
        if (empty($paths)) {
            return;
        }
        spl_autoload_register(function (string $class) use ($paths) {

            $file = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            foreach ($paths as $path) {
                if (is_file($path . $file)) {
                    include($path . $file);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * 获取指定对象
     * @param $name
     * @return mixed|object
     * @throws Core\Di\ObjectNotFoundException
     */
    public function get(string $name)
    {
        if (isset($this->workerClassMap[$name])) {
            return $this->container->get($this->workerClassMap[$name]);
        }
        return $this->container->get($name);
    }

    /**
     * 获取配置
     * @param string $name
     * @param bool $throw
     *
     * @return array|mixed
     * @throws \Kernel\Core\Conf\ConfigNotFoundException
     */
    public function getConfig(string $name, bool $throw = false)
    {
        return self::$config->get($name, $throw);
    }

    /**
     * @param Server $server
     */
    public function serverStart(Server $server)
    {
        $server->start();
    }

    /**
     * 獲取容器
     * @return IContainer
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getWorkerStartClassName(string $name): string
    {
        return isset($this->workerClassMap[$name]) ? $this->workerClassMap[$name] : '';
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }
}