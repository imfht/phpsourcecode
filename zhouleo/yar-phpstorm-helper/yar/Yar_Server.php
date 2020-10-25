<?php

/**
 * The Yar_Server class
 */
class Yar_Server
{
    protected $_executor;

    /**
     * 创建一个Yar的HTTP RPC服务，参数$obj对象的所有公开方法都会被注册为服务函数，可以被RPC调用
     *
     * @param Object $obj 一个对象实例，这个对象的所有公开方法都会被注册为服务函数
     * @return Yar_Server 返回一个Yar_Server的实例
     */
    final public function __construct($obj)
    {
    }

    /**
     * 启动服务，开始接受客户端的调用请求.
     *
     * @return boolean
     */
    public function handle()
    {
    }
}
