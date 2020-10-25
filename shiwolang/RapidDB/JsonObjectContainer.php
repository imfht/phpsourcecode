<?php
/**
 * Created by zhouzhongyuan.
 * User: zhou
 * Date: 2015/11/27
 * Time: 11:43
 */

namespace shiwolang\db;


class JsonObjectContainer implements \JsonSerializable, ObjectContainerInterface
{
    protected $className       = null;
    protected $object          = null;
    protected $reflectionClass = null;
    protected $data            = [];

    public function __construct($className = null, $constructArgs = [])
    {
        $this->className = $className;
        if ($className !== null) {
            $this->reflectionClass = new \ReflectionClass($className);
            $this->object          = $this->reflectionClass->newInstanceArgs($constructArgs);
            foreach ($this->data as $name => $value) {
                $this->object->$name = $value;
            }
        }

    }

    function __set($name, $value)
    {
        $this->data[$name] = $value;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        $data    = [];
        $methods = $this->reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $key => $method) {
            if (strpos($method->getDocComment(), "@json") !== false) {
                $name               = $method->getName();
                $jsonKeyName        = lcfirst(strpos($name, "get") == 0 ? substr($name, 3) : $name);
                $data[$jsonKeyName] = $method->invoke($this->object);
            }
        }
        $data = count($data) == 0 ? $this->data : $data;

        return $data;
    }

    /**
     * @return null|object
     */
    public function getObject()
    {
        return $this->object;
    }

    public function setObject($value)
    {
        $this->object = $value;
    }
}
