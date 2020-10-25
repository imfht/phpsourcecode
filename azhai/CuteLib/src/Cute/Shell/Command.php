<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Shell;


/**
 * 控制台命令
 */
abstract class Command
{
    public $cmdfile = '';
    protected $app = null;
    protected $args = [];
    protected $pid = 0;

    public function __construct($app, $cmdfile, array $argv = [])
    {
        $this->app = $app;
        $this->cmdfile = $cmdfile;
        list($args, $opts) = self::parse($argv);
        $this->args = array_merge($args, $opts);
    }

    /**
     * Parses the array and returns a tuple containing the arguments and the options
     *
     * @param array $argv
     * @return array
     */
    public static function parse(array $argv)
    {
        $args = [];
        $opts = [];
        foreach ($argv as $i => $arg) {
            $arg = trim($arg);
            if ($arg === '--') {
                $args[] = implode(' ', array_slice($argv, $i + 1));
                break;
            }
            if (substr($arg, 0, 1) !== '-') { //没有-开头
                $args[] = $arg;
            } else if (substr($arg, 1, 1) !== '-') { //以-开头，后面被认为是单字符
                $keys = str_split(substr($arg, 1)); //多个字符会被分解
                foreach ($keys as $key) {
                    $opts[$key] = true;
                }
            } else { //以--或更多开头
                if (($sep = strpos($arg, '=')) !== false) {
                    $key = substr($arg, 2, $sep - 2);
                    $value = substr($arg, $sep + 1);
                } else {
                    $key = substr($arg, 2);
                    $value = true;
                }
                if (!array_key_exists($key, $opts)) {
                    $opts[$key] = $value;
                } else { //已存在键，转为列表
                    if (!is_array($opts[$key])) {
                        $opts[$key] = [$opts[$key]];
                    }
                    $opts[$key][] = $value;
                }
            }
        }
        return [$args, $opts];
    }

    public function __clone()
    {
        $this->args = array_change_key_case($this->args);
    }

    public function __toString()
    {
        $args = [];
        foreach ($this->args as $key => $value) {
            if (is_numeric($key)) {
                $args[] = $value;
            } else if ($value === true) {
                $args[] = strlen($key) === 1 ? " -$key" : " --$key";
            } else {
                $args[] = " --$key='$value'";
            }
        }
        $program = $this->cmdfile;
        $program .= ' ' . substr(__CLASS__, 0, -strlen('Command'));
        $program .= ' ' . implode(' ', $args);
        return $program;
    }

    public function addArg($value, $key = false)
    {
        if ($key === false || is_null($key)) {
            $this->args[] = $value;
        } else {
            $this->args[$key] = $value;
        }
        return $this;
    }

    public function getArg($key = false, $default = null)
    {
        if ($key === false || is_null($key)) {
            return $this->args;
        }
        if (isset($this->args[$key])) {
            return $this->args[$key];
        } else {
            return $default;
        }
    }

    public function popArg($key)
    {
        if ($key === false || is_null($key)) {
            return;
        }
        if (isset($this->args[$key])) {
            $value = $this->args[$key];
            unset($this->args[$key]);
            return $value;
        }
    }

    public function readPid($pidfile = null)
    {
        if (!is_null($pidfile) && is_readable($pidfile)) {
            $pid = trim(file_get_contents($pidfile));
            if (is_numeric($pid)) {
                $this->pid = $pid;
            }
        }
        return $this->pid;
    }

    public function wait($sleep_secs = 0.1)
    {
        while ($this->isRunning()) {
            usleep($sleep_secs * 1000000); //暂停$sleep_secs秒
        }
        return true;
    }

    public function isRunning()
    {
        if ($this->pid > 0) {
            try {
                $result = shell_exec(sprintf('ps %d', $this->pid));
                return substr_count($result, "\n") >= 2;
            } catch (\Exception $e) {
            }
        }
        return false;
    }

    //停止进程：null-已停止过 true-停止成功 false-停止失败

    public function stop()
    {
        if ($this->pid > 0 && $this->isRunning()) {
            return posix_kill($this->pid, SIGTERM);
        }
    }

    public function fork($pidfile = null, array $extra = [])
    {
        $clone = clone $this;
        $clone->args = array_merge($clone->args, $extra);
        $program = strval($clone);
        $app = $clone->app;
        if ($app->outfile !== 1) {
            $program .= ' > ' . $app->getFileSymbol($app->outfile, true);
        }
        if ($app->errfile !== 2) {
            $program .= ' 2 > ' . $app->getFileSymbol($app->errfile, true);
        }
        if ($pid = shell_exec($program . ' & echo $!')) {
            $pid = trim($pid);
            $clone->pid = intval($pid);
            if (!is_null($pidfile)) {
                file_put_contents($pidfile, $pid . PHP_EOL, LOCK_EX);
            }
        }
        return $clone->pid;
    }

    public function loop($times = 1, $expire = 0, $sleep = 0)
    {
        if ($expire > 0) {
            $expire = intval($expire) + time();
        }
        do {
            $this->execute();
            if ($times > 0) { //计次
                $times--;
            }
            if ($sleep > 0) { //休眠间隔
                usleep($sleep * 1000000);
            }
        } while ($times === 0 || $expire < 0 || $expire < time());
    }

    abstract public function execute();
}
