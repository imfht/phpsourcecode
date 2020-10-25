<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Daemon;


use Timo\Config\Config;
use Timo\Core\App;
use Timo\Core\Container;
use Timo\Core\Db;
use Timo\Loader;

class Daemon
{
    /**
     * @var int 0异常退出 1正常退出 2重启退出 3正常退出不重启 4捕获到异常正常退出
     */
    protected $exit_mode = 0;

    /**
     * @var int 启动时间
     */
    protected $start_time = 0;

    /**
     * @var int
     */
    protected $db_destroy_time = 0;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var int 执行的任务数
     */
    protected $count = 0;

    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化
     */
    protected function init()
    {
        static::log('worker: ' . getmypid() . ' running...');
        $this->start_time = time();
        $this->db_destroy_time = $this->start_time;
        $this->container = App::container();

        if (!IS_WIN) {
            register_shutdown_function([$this, 'callRegisteredShutdown']);
            pcntl_signal(SIGUSR1, [$this, 'signalHandler']);
            pcntl_signal(SIGUSR2, [$this, 'signalHandler']);
        }
    }

    /**
     * 进程超过6小时后重启
     *
     * @param int $hour
     */
    protected function timeoutRestart($hour = 6)
    {
        if ((time() - $this->start_time) > 3600 * $hour) {
            $this->normalExit('timeout');
        }
    }

    /**
     * 每天几点重启
     *
     * @param int $hour 默认值3点
     */
    protected function loopRestart($hour = 3)
    {
        $c = (int)date('His');
        $s = intval($hour . '0000');
        $e = intval($hour . '0005');
        if ($s <= $c && $c <= $e) {
            sleep(10);
            $this->normalExit('loop');
        }
    }

    /**
     * 处理多少次后重启
     *
     * @param $num
     */
    protected function fullRestart($num)
    {
        $this->count++;
        if ($this->count == $num) {
            $this->normalExit($num . ' times');
        }
    }

    /**
     * 每一分钟销毁一次mysql连接
     */
    protected function dbDestroy()
    {
        $current_time = time();
        if ($current_time - $this->db_destroy_time > 60) {
            Db::destroy();
            Loader::destroy();
            $this->db_destroy_time = $current_time;
        }
    }

    /**
     * 正常退出
     *
     * @param string $sign 退出标识
     */
    protected function normalExit($sign = '')
    {
        $this->exit_mode = 1;
        $this->restart();
        die(static::log('worker: ' . getmypid() . ' ' . $sign . ' normal exit restart', true));
    }

    /**
     * 捕获到异常后重启退出
     */
    protected function catchExceptionExit()
    {
        $this->exit_mode = 4;
        $this->restart();
        die(static::log('worker: ' . getmypid() . ' exception exit', true));
    }

    /**
     * 异常退出
     */
    public function callRegisteredShutdown()
    {
        switch ($this->exit_mode) {
            case 0:
                static::log('worker: ' . getmypid() . ' not normal exit');
                $this->restart();
                break;
        }
        return true;
    }

    /**
     * 信号处理
     *
     * @param $signal
     */
    public function signalHandler($signal)
    {
        switch ($signal) {
            case SIGUSR1:
                $this->exit_mode = 2;
                $this->restart();
                die(static::log('worker: ' . getmypid() . ' restart exit', true));
                break;
            case SIGUSR2:
                $this->exit_mode = 3;
                die(static::log('worker: ' . getmypid() . ' stop exit', true));
                break;
        }
    }

    /**
     * 重启进程
     */
    protected function restart()
    {
        $script = Config::runtime('cli.entry_path');
        $operate = lcfirst(App::controller()) . '/run';
        $log_file = '/data/log/worker/gumaor_' . ENV . '_' . lcfirst(App::controller()) . '.log';

        $cmd = 'nohup /usr/local/php/bin/php ' . $script . ' ' . $operate . ' >> ' . $log_file . ' 2>&1 &';

        exec($cmd);
    }

    /**
     * 输出运行日志
     *
     * @param $str
     * @param bool $return
     * @return bool|string
     */
    public static function log($str, $return = false)
    {
        $str = '[' . date('Y-m-d H:i:s') . '] ' . $str . PHP_EOL;
        if (!$return) {
            echo $str;
            return true;
        }
        return $str;
    }
}
