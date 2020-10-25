<?php

namespace shiwolang\base;
/**
 * Created by zhouzhongyuan.
 * User: zhouzhongyuan
 * Date: 2015/11/26
 * Time: 17:01
 */
class Object
{
    public static $__mro__ = null;

    public    $supers  = [];
    protected $_supers = [];

    /**
     * @return static
     */
    public static function init()
    {
        $args = func_get_args();
        $self = (new \ReflectionClass(static::className()))->newInstanceArgs($args);
        foreach (static::__mro__() as $className) {
            $r      = new \ReflectionClass($className);
            $object = $r->newInstanceArgs($args);
            if ($object instanceof Object) {
                $self->super($className, $object);
            }
        }
		
		$super->_supers = $self->supers;

        return $self;
    }

    public function super($className, Object $object = null)
    {
        if ($object !== null) {
            $extends = static::__mro__();

            if (in_array($className, $extends)) {
                return $this->supers[$className] = $object;
            } else {
                throw new \Exception(static::className() . " does not extend from " . $className);
            }
        } else {
            return $this->_supers[$className];
        }
    }


    protected static function extend()
    {
        $parentClass = func_get_args();

        if (empty($parentClass)) {
            return static::className() == Object::className() ? [] : [Object::className()];
        } else {
            return $parentClass;
        }
    }

    public static function __mro__()
    {
        if (static::$__mro__ === null) {
            static::$__mro__ = static::__get_mro__();
            static::$__mro__ = array_slice(static::$__mro__, 1, -1);
        }

        return static::$__mro__;
    }

    public static function __get_mro__($classes = null)
    {
        $classes === null && $classes = [static::className()];
        /** @var Object[] $classes */
        if (count($classes) == 1) {
            if (count($classes[0]::extend()) == 0) {
                return $classes;
            } else {
                return array_merge($classes, static::__get_mro__($classes[0]::extend()));
            }
        } else {
            $list = [];
            foreach ($classes as $class) {
                array_push($list, static::__get_mro__([$class]));
            }
            $seqs     = array_merge($list, [$classes]);
            $res      = [];
            $seqsList = $seqs;
            while (true) {
                $seqsList = array_filter($seqsList);
                if (empty($seqsList)) {
                    return $res;
                }
                foreach ($seqsList as $seq) {
                    $candidate = current($seq);
                    $not_head  = [];
                    foreach ($seqsList as $s) {
                        in_array($candidate, array_slice($s, 1)) && array_push($not_head, $s);
                    }
                    if (!empty($not_head)) {
                        $candidate = null;
                    } else {
                        break;
                    }
                }
                if (empty($candidate)) {
                    throw new \Exception("get resolution order error! please check your extend!");
                }
                array_push($res, $candidate);
                foreach ($seqsList as $key => $seq1) {
                    if (current($seq1) == $candidate) {
                        unset($seqsList[$key][current(array_keys($seq1))]);
                    }
                }
            }
        }
        throw new \Exception("get resolution order error! please check your extend!");
    }

    public static function className()
    {
        return get_called_class();
    }

    protected static function camelName($name, $ucfirst = true)
    {
        if (strpos($name, "_") !== false) {
            $name = str_replace("_", " ", strtolower($name));
            $name = ucwords($name);
            $name = str_replace(" ", "", $name);
        }

        return $ucfirst ? ucfirst($name) : $name;
    }

    public function __get($name)
    {
        $getter = 'get' . self::camelName($name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        foreach ($this->supers as $super) {
            if (property_exists($super, $name)) {
                return $super->$name;
            }
            if (method_exists($super, $getter)) {
                return $super->$getter();
            }
        }

        throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    public function __set($name, $value)
    {
        $setter = 'set' . self::camelName($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }
        foreach ($this->supers as $super) {
            if (property_exists($super, $name)) {
                $super->$name = $value;

                return;
            }
            if (method_exists($super, $setter)) {
                $super->$setter($value);

                return;
            }
        }

        throw new \Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
    }

    public function __isset($name)
    {
        $getter = 'get' . self::camelName($name);
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        foreach ($this->supers as $super) {
            if (property_exists($super, $name)) {
                return true;
            }
            if (method_exists($super, $getter)) {
                return $super->$getter() !== null;
            }
        }

        return false;
    }


    public function __unset($name)
    {
        $setter = 'set' . self::camelName($name);
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        }
        foreach ($this->supers as $super) {
            if (property_exists($super, $name)) {
                unset($super->$name);
            }
            if (method_exists($super, $setter)) {
                return $super->$setter(null);
            }
        }
        throw new \Exception('Unsetting property: ' . get_class($this) . '::' . $name);
    }


    public function __call($name, $params)
    {
        foreach ($this->supers as $super) {
            if (method_exists($super, $name)) {
                return call_user_func_array([$super, $name], $params);
            }
        }
        throw new \Exception('Calling unknown method: ' . get_class($this) . "::$name()");
    }


    public static function __callStatic($name, $arguments)
    {
        foreach (static::__mro__() as $super) {
            if (method_exists($super, $name)) {
                return call_user_func_array([$super, $name], $arguments);
            }
        }
        throw new \Exception('Calling unknown static method: ' . static::className() . "::$name()");
    }
}