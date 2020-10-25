<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 15-8-30
 * Time: 下午2:45
 */

namespace framework\core;


class Object
{

    /**
     * 返回当前类的 完整名称
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }
    /**
     * 获取属性值
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        $func = 'get'.ucfirst($name);
        if(method_exists($this, $func)){
            return $this->$func();
        }elseif(method_exists($this, 'set'.ucfirst($name))){
            throw new \Exception('getting write=only property: '.get_class($this).'::'.$name);
        }else {
            throw new \Exception('getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 设置属性值
     * @param $name
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $func = 'set'.ucfirst($name);
        if(method_exists($this, $name)){
            return $this->$func($value);
        }elseif(method_exists($this, 'get'.ucfirst($name))){
            throw new \Exception('setting read-only property: '.get_class($this).'::'.$name);
        }else {
            throw new \Exception('setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 判断属性是否存在
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $func = 'get'.ucfirst($name);
        if(method_exists($this, $func)){
            return $this->$func() !== null;
        }else{
            return false;
        }
    }

    /**
     * 把一个属性设为 null
     * @param $name
     * @throws \Exception
     */
    public function __unset($name)
    {
        $func = 'set'.ucfirst($name);
        if(method_exists($this, $func)){
            $this->$func(null);
        }elseif(method_exists($this, 'get'.ucfirst($name))){
            throw new \Exception('unsetting read-only property: '.get_class($this).'::'.$name);
        }
    }

    /**
     * 检查属性是否存在
     * @param $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return property_exists($this, $name) || method_exists($this, 'get'.ucfirst($name))
        || method_exists($this, 'set'.ucfirst($name));
    }
    /**
     * 检查当前类中是都邮制定的方法
     * @param $name
     * @return bool
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }
}