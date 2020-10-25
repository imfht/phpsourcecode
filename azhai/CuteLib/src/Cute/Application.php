<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute;

use \Cute\Importer;
use \Cute\Base\Factory;
use \Cute\Base\Storage;


/**
 * 应用环境
 */
class Application
{
    const APP_SECTION = 'app';

    public static $app = null;
    protected $import = null;
    protected $storage = null; // 配置
    protected $shortcuts = []; // 快捷方式

    /**
     * 构造函数
     */
    public function __construct(array $data = null)
    {
        self::throwWarnings();
        self::$app = $this;
        $this->importer = Importer::getInstance();
        $this->storage = new Storage($data);
        $this->initiate();
    }
    
    public static function throwWarnings()
    {
        //只拦截警告，并以异常形式抛出
        set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcxt) {
            if (0 === error_reporting()) {
                return false; // error was suppressed with the @-operator
            }
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }, E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING);
    }

    /**
     * 初始化环境
     */
    public function initiate()
    {
        $this->install($this->importer, [
            'import' => 'addNamespace',
            'importStrip' => 'addNamespaceStrip',
        ]);
        $factory = new Factory($this->storage);
        $this->installRef($factory, ['create', 'load']);
    }

    /**
     * 安装插件，并注册插件的一些方法
     */
    public function install($plugin, array $methods)
    {
        foreach ($methods as $alias => $method) {
            //省略别名时，使用同名方法。PHP的方法名内部都是小写？
            $alias = strtolower(is_numeric($alias) ? $method : $alias);
            $this->shortcuts[$alias] = [$plugin, $method];
        }
        return $this;
    }

    /**
     * 安装插件引用，并注册插件的一些方法
     */
    public function installRef(& $plugin, array $methods)
    {
        foreach ($methods as $method) {
            $this->shortcuts[strtolower($method)] = & $plugin;
        }
        return $this;
    }

    /**
     * 使用已定义的插件
     */
    public function __call($name, $args)
    {
        $name = strtolower($name); //PHP的方法名内部都是小写？
        if (isset($this->shortcuts[$name])) {
            $shortcut = $this->shortcuts[$name];
            if (is_array($shortcut)) {
                @list($plugin, $name) = $shortcut;
            } else {
                $plugin = &$this->shortcuts[$name];
            }
            return exec_method_array($plugin, $name, $args);
        }
    }

    /**
     * 获取公开配置信息
     */
    public function getConfig($key, $default = null)
    {
        $section = $this->storage->getSectionOnce(self::APP_SECTION);
        return $section->getItem($key, $default);
    }
}
