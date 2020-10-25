<?php
/**
 * 控制器层基类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/4/16
 * Time: 17:38
 */

namespace Bjask;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class Controller
{
    private $extras = [];
    private static $config = null;
    private static $log = null;
    private static $container = null;

    public function __construct()
    {
        if (is_null(self::$config)) {
            self::$config = Config::load();
        }
        if (is_null(self::$log)) {
            self::$log = Logger::getInstance($this->getConfig('log'));
        }
        if (is_null(self::$container)) {
            self::$container = new ContainerBuilder();
        }
        self::$container->register('doctrine', '\\Bjask\\Doctrine')
            ->addMethodCall('create', [$this->getConfig('database')]);
    }

    /**
     * 从容器中获取Doctrine
     * @return Doctrine
     */
    public function getDoctrine(): Doctrine
    {
        return self::$container->get('doctrine');
    }

    /**
     * 写日志
     * @param string $message
     * @return bool
     */
    public function log(string $message)
    {
        return self::$log->log(sprintf('[task running]：%s', $message));
    }

    /**
     * 获取系统配置
     * @param string $key
     * @return array|mixed|string
     */
    public function getConfig($key = '')
    {
        return self::$config->get($key);
    }

    /**
     * @param array $extras
     */
    final public function setExtras(array $extras)
    {
        $this->extras = $extras;
    }

    final public function getExtras()
    {
        return $this->extras;
    }

}