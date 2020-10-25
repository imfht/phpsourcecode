<?php
namespace Yurun\Swoole\SharedMemory;

use Yurun\Swoole\SharedMemory\Message\Operation;

class OperationParser
{
    /**
     * 对象集合
     *
     * @var array
     */
    private $objects = [];

    public function __construct($storeTypes)
    {
        foreach($storeTypes as $k => $v)
        {
            if(is_numeric($k))
            {
                $refClass = new \ReflectionClass($v);
                $this->objects[$refClass->getShortName()] = new $v;
            }
            else
            {
                $this->objects[$k] = new $v;
            }
        }
    }

    public function parse(Operation $body)
    {
        if(!isset($this->objects[$body->object]))
        {
            throw new \RuntimeException(sprintf('Has no object %s', $body->object));
        }
        return ($this->objects[$body->object])->{$body->operation}(...$body->args);
    }
}