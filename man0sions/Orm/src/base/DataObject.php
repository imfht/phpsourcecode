<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/10/12
 * Time: 上午11:19
 */

namespace LuciferP\Orm\base;


class DataObject implements \ArrayAccess
{
    private $object;
    protected $id = null;

    public function __construct($id = null)
    {
        if ($id) {
            $this->id = $id;
        }
    }

    public function setAttributes($data)
    {
        foreach ($data ? $data : [] as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $this->$key = $value;

        }
    }

    public function offsetExists($offset)
    {
        return isset($this->object[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->object[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->object[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->object[$offset]);
    }

    public function __set($name, $value)
    {
        $this->object[$name] = $value;
    }

    public function __get($name)
    {
        return $this->object[$name];
    }
}