<?php

class Core
{
    static $classMap = [];

    static $config = [];

    static $dispatcher;

    /**
     * @param $config_addr
     * @throws \Exception
     */
    public static function run($config_addr)
    {
        if (!file_exists($config_addr)) {
            throw new \Exception('config file was not found');
        }
        $public_config = require($config_addr);
        date_default_timezone_set($public_config['timezone']);
        IS_CLI && \Signal::init();
        \Logger::iniSet($public_config['logs']);
        static::setConfig($public_config);
        static::setDispatcher(\Dispatcher::instance(\Router::instance($public_config['route']), $public_config));
    }

    /**
     * @return \Dispatcher
     */
    public function getDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * @param \Dispatcher $dispatcher
     */
    public static function setDispatcher(\Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * @param $config_array
     */
    public static function setConfig($config_array)
    {
        static::$config = $config_array;
    }

    /**
     * @param $field
     * @return mixed
     * @throws \Exception
     */
    public static function getConfig($field)
    {
        if (!isset(static::$config[$field])) {
            throw new \Exception('No configuration');
        }

        return static::$config[$field];
    }

    /**
     * 已弃用 改为composer 自动加载
     * @param $class
     * @return bool
     */
    public static function load($class)
    {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        if (!isset(static::$classMap[$class])) {
            if (file_exists(LEN_DIR . $class . EXT)) {
                include LEN_DIR . $class . EXT;
            } else {
                return false;
            }
            static::$classMap[$class] = $class;
        }

        return true;
    }

    /**
     * @param \Exception $exception
     */
    public static function ErrorHandler(\Exception $exception)
    {
        $error_info = array(
            'message' => $exception->getMessage(),
            'line' => $exception->getFile(),
            'file' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        );
        \Logger::l(\Logger::ERR, $error_info);
        \Output\Error::output($exception->getCode(), $exception->getMessage());
    }

    public static function _whoopsRegister()
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }
}
