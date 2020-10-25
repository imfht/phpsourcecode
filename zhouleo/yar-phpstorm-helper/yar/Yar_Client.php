<?php

/**
 * The Yar_Client class
 */
class Yar_Client
{
    protected $_protocol;
    protected $_uri;
    protected $_options;
    protected $_running;

    /**
     * 发起一个RPC调用，并且得到返回值。如果服务端的远程调用抛出异常，那么本地也会相应的抛出一个Yar_Server_Exception异常
     *
     * @param string $method 远程服务的名字
     * @param array $parameters 调用参数
     * @return void
     */
    public function __call($method, array $parameters)
    {
    }

    /**
     * 创建一个客户端实例
     *
     * @param string $url
     * @return Yar_Client 实例
     */
    final public function __construct($url)
    {
    }

    /**
     * 设置调用远程服务的一些配置，比如超时值，打包类型等
     *
     * @param number $name
     * @param mixed $value
     * @return boolean
     */
    public function setOpt($name, $value)
    {
    }
}