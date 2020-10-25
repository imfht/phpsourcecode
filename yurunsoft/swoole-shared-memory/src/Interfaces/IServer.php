<?php
namespace Yurun\Swoole\SharedMemory\Interfaces;

interface IServer
{
    /**
     * 构造方法
     *
     * @param array $options 配置
     */
    public function __construct($options = []);

    /**
     * 运行服务器
     *
     * @return void
     */
    public function run();

    /**
     * 获取配置
     *
     * @return void
     */
    public function getOptions();

    /**
     * 设置配置
     *
     * @param array $options
     * @return void
     */
    public function setOptions($options);
}