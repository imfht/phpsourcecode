<?php
namespace Yurun\Swoole\SharedMemory\Message;

class Result
{
    /**
     * 结果
     *
     * @var mixed
     */
    public $result;

    /**
     * 错误或异常对象
     *
     * @var \Throwable
     */
    public $throwable;

    public function __construct($result, $throwable = null)
    {
        $this->result = $result;
        $this->throwable = $throwable;
    }
}