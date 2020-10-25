<?php

/**
 * The Yar_Server_Exception class
 */
class Yar_Server_Exception extends Exception
{
    protected $_type;

    /**
     * 当服务端的服务函数抛出异常的时候，客户端本地会响应的抛出一个Yar_Server_Exception异常。有一个属性，标明了服务端异常的类型。这个方法就是获取这个异常类型
     *
     * @return string
     */
    public function getType()
    {
    }
}