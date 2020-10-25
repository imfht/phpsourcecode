<?php
namespace Yurun\Swoole\SharedMemory\Interfaces;

use Yurun\Swoole\SharedMemory\Message\Operation;
use Yurun\Swoole\SharedMemory\Message\Result;


interface IClient
{
    /**
     * 构造方法
     *
     * @param array $options 配置
     */
    public function __construct($options = []);

    /**
     * 连接
     *
     * @return boolean
     */
    public function connect(): bool;

    /**
     * 发送操作
     *
     * @param \Yurun\Swoole\SharedMemory\Message\Operation $operation
     * @return boolean
     */
    public function send(Operation $operation): bool;

    /**
     * 接收结果
     *
     * @return \Yurun\Swoole\SharedMemory\Message\Result|boolean
     */
    public function recv();
}