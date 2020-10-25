<?php
namespace Yurun\Swoole\SharedMemory\Message;

class Operation
{
    /**
     * 操作的对象名
     *
     * @var string
     */
    public $object;

    /**
     * 操作名
     *
     * @var string
     */
    public $operation;

    /**
     * 参数
     *
     * @var array
     */
    public $args;

    public function __construct(string $object, string $operation, array $args = [])
    {
        $this->object = $object;
        $this->operation = $operation;
        $this->args = $args;
    }
}