<?php

/**
 * The Yar_Concurrent_Client class
 */
class Yar_Concurrent_Client
{
    static $_callstack;
    static $_callback;
    static $_error_callback;

    /**
     * 注册一个并行的(异步的)远程服务调用，不过这个调用请求不会被立即发出，而是会在接下来调用Yar_Concurrent_Client::loop()的时候才真正的发送出去
     *
     * @param string $uri
     * @param string $method
     * @param array $parameters
     * @param callable $callback [optional]
     *
     * @return int
     */
    public static function call($uri, $method, array $parameters, callable $callback)
    {
    }

    /**
     * 发送所有的已经通过 Yar_Concurrent_Client::call() 注册的并行调用，并且等待返回
     *
     * @param callable $callback [optional]
     * @param callable $error_callback [optional]
     *
     * @return boolean
     */
    public static function loop(callable $callback, callable $error_callback)
    {
    }

}