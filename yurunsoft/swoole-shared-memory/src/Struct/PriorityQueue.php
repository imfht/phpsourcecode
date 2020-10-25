<?php
namespace Yurun\Swoole\SharedMemory\Struct;

class PriorityQueue extends \SplPriorityQueue implements \Serializable
{
    public function serialize()
    {
        return serialize(iterator_to_array(clone $this));
    }

    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        foreach($array as $p => $v)
        {
            $this->insert($v, $p);
        }
    }
}