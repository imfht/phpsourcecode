<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Shell;

use \Cute\Application;
use \Cute\Shell\Command;
use \Cute\Utility\Inflect;


/**
 * 控制台
 */
class Console extends Application
{
    const STDIN = 'php://stdin';
    const STDOUT = 'php://stdout';
    const STDERR = 'php://stderr';
    const DEVNULL = '/dev/null';

    const COLOR_BLACK = 90;
    const COLOR_RED = 91;
    const COLOR_GREEN = 92;
    const COLOR_YELLOW = 93;
    const COLOR_BLUE = 94;
    const COLOR_MAGENTA = 95;
    const COLOR_CYAN = 96;
    const COLOR_WHITE = 97;
    public $outfile = 1;
    public $errfile = null;
    protected $color = false;
    protected $auto_ns = false; //自动套用Namespace

    /**
     * 初始化环境
     */
    public function initiate()
    {
        parent::initiate();
        return $this;
    }

    public function setAutoNS($auto_ns)
    {
        $this->auto_ns = $auto_ns;
        return $this;
    }

    public function setColor($color)
    {
        if (is_string($color)) {
            $color = 'COLOR_' . strtoupper($color);
            $this->color = constant(__CLASS__ . '::' . $color);
        }
        return $this;
    }

    public function write($text)
    {
        $text = strval($text);
        if (func_num_args() > 1) {
            $args = array_slice(func_get_args(), 1);
            $text = vsprintf($text, $args);
        }
        return self::appendTo($text, $this->color, $this->outfile);
    }

    public static function appendTo($text, $color = false, $outfile = null)
    {
        if (empty($outfile) && $outfile !== 0) {
            return 0;
        }
        if (is_int($outfile) && is_int($color)) {
            $text = sprintf("\033[%dm%s\033[%dm", $color, $text, 0);
        }
        $outfile = self::getFileSymbol($outfile);
        return file_put_contents($outfile, $text, FILE_APPEND | LOCK_EX);
    }

    public static function getFileSymbol($file, $is_shell = false)
    {
        if (is_null($file)) {
            return self::DEVNULL;
        } else if (is_int($file)) {
            if ($file === 0) {
                return $is_shell ? '&0' : self::STDIN;
            } else if ($file === 1) {
                return $is_shell ? '&1' : self::STDOUT;
            } else if ($file === 2) {
                return $is_shell ? '&2' : self::STDERR;
            }
        }
        return strval($file);
    }

    public function writeln($text)
    {
        $args = func_get_args();
        $args[0] .= PHP_EOL;
        return exec_method_array($this, 'write', $args);
    }

    public function mount($path)
    {
        $pathes = func_get_args();
        foreach ($pathes as & $path) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        }
        $appends = PATH_SEPARATOR . implode(PATH_SEPARATOR, $pathes);
        set_include_path(get_include_path() . $appends);
        return $this;
    }

    public static function getDeclaredClass($target)
    {
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if (ends_with($class, $target)) {
                return $class;
            }
        }
    }

    public function run()
    {
        if (php_sapi_name() !== 'cli') { // 仅运行于命令行模式
            return;
        }
        if ($_SERVER['argc'] < 2) { //参数不足
            return;
        }
        $argv = $_SERVER['argv'];
        $cmdfile = array_shift($argv);
        $name = array_shift($argv);
        $class = Inflect::camelize($name) . 'Command';
        require_once $class . '.php';
        if ($this->auto_ns) {
            $class = self::getDeclaredClass($class);
        } else {
            $class = class_exists($class) ? $class : null;
        }
        if ($class) {
            $object = new $class($this, $cmdfile, $argv);
            return $object->execute();
        }
    }
}
