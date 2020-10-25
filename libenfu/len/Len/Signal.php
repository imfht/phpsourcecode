<?php

/**
 * Class Signal
 * 信号量
 */
class Signal
{
    private static $signal;

    public static function init()
    {
        if (!isset(static::$signal)) {
            static::$signal = new self();
        }

        return static::$signal;
    }

    /**
     * kill 9
     * @param $signal
     */
    private static function signalHandler($signal)
    {
        Logger::l(Logger::DEBUG, array('signal' => $signal));
        posix_kill(posix_getpid(), SIGKILL);
    }

    /**
     * Signal constructor.
     * @throws \Exception
     */
    private function __construct()
    {
        if (!IS_CLI) {
            throw new \Exception('Must CLI Model');
        }
        static::pcntl_signal();
    }

    /**
     * 安装信号处理器 kill 2
     */
    private static function pcntl_signal()
    {
        // 三参数必须为false 缺省参数存在bug --disable-posix
        pcntl_signal(SIGINT, array('\Signal', 'signalHandler'), false);
    }

    /**
     * 调用等待信号的处理器
     */
    public static function dispatch()
    {
        pcntl_signal_dispatch();
    }
}