<?php

namespace fluiex;

use fluiex\ErrorHandler;
use fluiex\Memory;

class F
{

    private static $_imports;
    private static $_app;

    /**
     *
     * @var Memory 
     */
    private static $_memory;

    public static function start()
    {
        error_reporting(E_ERROR);

        !defined('IN_DISCUZ') && define('IN_DISCUZ', true);
        !defined('DISCUZ_ROOT') && define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -12));
        !defined('DISCUZ_CORE_DEBUG') && define('DISCUZ_CORE_DEBUG', false);

        spl_autoload_register(array(__CLASS__, 'autoload'));

        set_exception_handler(array(__CLASS__, 'handleException'));

        if (DISCUZ_CORE_DEBUG) {
            set_error_handler(array(__CLASS__, 'handleError'));
            register_shutdown_function(array(__CLASS__, 'handleShutdown'));
        }

        static::creatwebapp();
    }

    /**
     * 
     * @return Application
     */
    public static function app()
    {
        return self::$_app;
    }
    
    /**
     * 
     * @param string $class 需完整类名,含命名空间
     * @return mixed
     */
    public static function creatwebapp()
    {
        if (!is_object(self::$_app)) {

            self::$_app = \fluiex\web\Application::instance();
        }
        return self::$_app;
    }

    public static function memory()
    {
        if (!self::$_memory) {
            self::$_memory = new Memory();
            self::$_memory->init(self::app()->config['memory']);
        }
        return self::$_memory;
    }

    /**
     * 引入文件,主要用于引入一些函数库
     * @param type $name
     * @param type $folder
     * @param type $force
     * @return boolean
     * @throws Exception
     */
    public static function import($name, $folder = '', $force = false)
    {
        $key = $folder . $name;
        if (isset(self::$_imports[$key])) {
            return true;
        }
        $path = DISCUZ_ROOT . '/source/' . $folder;
        if (strpos($name, '/') !== false) {
            $pre = basename(dirname($name));
            $filename = dirname($name) . '/' . $pre . '_' . basename($name) . '.php';
        } else {
            $filename = $name . '.php';
        }

        if (is_file($path . '/' . $filename)) {
            include $path . '/' . $filename;
            self::$_imports[$key] = true;

            return true;
        }

        if (!$force) {
            return false;
        }

        throw new Exception('Oops! System file lost: ' . $filename);
    }

    public static function handleException($exception)
    {
        ErrorHandler::exception_error($exception);
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($errno && DISCUZ_CORE_DEBUG) {
            ErrorHandler::system_error($errstr, false, true, false);
        }
    }

    public static function handleShutdown()
    {
        if (($error = error_get_last()) && $error['type'] && DISCUZ_CORE_DEBUG) {
            ErrorHandler::system_error($error['message'], false, true, true);
        }
    }

    /**
     * 框架中类的自动加载
     * @param string $class
     * @return boolean
     */
    public static function autoload($class)
    {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (!file_exists($file)) {
            return false;
        }
        
        require $file;
        if (!class_exists($class, false)) {
            return false;
        }
        
        return true;
    }

    public static function analysisStart($name)
    {
        $key = 'other';
        if ($name[0] === '#') {
            list(, $key, $name) = explode('#', $name);
        }
        if (!isset($_ENV['analysis'])) {
            $_ENV['analysis'] = array();
        }
        if (!isset($_ENV['analysis'][$key])) {
            $_ENV['analysis'][$key] = array();
            $_ENV['analysis'][$key]['sum'] = 0;
        }
        $_ENV['analysis'][$key][$name]['start'] = microtime(TRUE);
        $_ENV['analysis'][$key][$name]['start_memory_get_usage'] = memory_get_usage();
        $_ENV['analysis'][$key][$name]['start_memory_get_real_usage'] = memory_get_usage(true);
        $_ENV['analysis'][$key][$name]['start_memory_get_peak_usage'] = memory_get_peak_usage();
        $_ENV['analysis'][$key][$name]['start_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
    }

    public static function analysisStop($name)
    {
        $key = 'other';
        if ($name[0] === '#') {
            list(, $key, $name) = explode('#', $name);
        }
        if (isset($_ENV['analysis'][$key][$name]['start'])) {
            $diff = round((microtime(TRUE) - $_ENV['analysis'][$key][$name]['start']) * 1000, 5);
            $_ENV['analysis'][$key][$name]['time'] = $diff;
            $_ENV['analysis'][$key]['sum'] = $_ENV['analysis'][$key]['sum'] + $diff;
            unset($_ENV['analysis'][$key][$name]['start']);
            $_ENV['analysis'][$key][$name]['stop_memory_get_usage'] = memory_get_usage();
            $_ENV['analysis'][$key][$name]['stop_memory_get_real_usage'] = memory_get_usage(true);
            $_ENV['analysis'][$key][$name]['stop_memory_get_peak_usage'] = memory_get_peak_usage();
            $_ENV['analysis'][$key][$name]['stop_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
        }
        return $_ENV['analysis'][$key][$name];
    }

}
