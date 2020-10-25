<?php
/**
 * Created by xiyusullos.
 * Created at 2017-03-22 13:13
 *
 * @author    xiyusullos <i@xy-jit.cc>
 * @copyright Copyright (C) 2017 xiyusullos.
 * @license   MIT
 */

namespace xiyusullos;

/**
 * Class Nullable
 * @package xiyusullos
 */
trait Nullable
{
    /**
     * Does nothing when invoking a inaccessible method in a static context.
     *
     * @param string $name The name of the method being called.
     * @param mixed[] $arguments The arguments of the method being called.
     *
     * @return Nil|mixed Always returns a new instance of static.
     */
    public static function __callStatic($name, $arguments)
    {
        return is_callable(['parent', '__callStatic']) ? parent::__callStatic($name, $arguments) : new Nil();
    }

    /**
     * Invokes this class as a function.
     *
     * @return Nil|mixed Always returns $this.
     */
    public function __invoke()
    {
        return is_callable(['parent', '__invoke']) ? parent::__invoke() : new Nil();
    }

    /**
     * Does nothing when invoking a inaccessible method in an object context.
     *
     * @param string $name The name of the method being called.
     * @param mixed[] $arguments The arguments of the method being called.
     *
     * @return Nil|mixed Always returns $this.
     */
    public function __call($name, $arguments)
    {
        return is_callable(['parent', '__call']) ? parent::__call($name, $arguments) : new Nil();
    }

    /**
     * Returns the value when reading data from a inaccessible property.
     *
     * @param string $name The property name.
     *
     * @return Nil|mixed Always returns $this.
     */
    public function __get($name)
    {
        $value = is_callable(['parent', '__get']) ? parent::__get($name) : new Nil();

        if (is_null($value)) {
            $value = new Nil();
        }

        return $value;
    }

    /**
     * Does nothing when writing data to a inaccessible property.
     *
     * @param string $name The property name to set the value to.
     * @param mixed $value The value to set.
     */
    public function __set($name, $value)
    {
        is_callable(['parent', '__set']) ? parent::__set($name, $value) : '';
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string Always returns the string representation of null.
     */
    public function __toString()
    {
        return is_callable(['parent', '__toString']) ? parent::__toString() : '';
    }

    /**
     * Does nothing with a clone operation.
     */
    public function __clone()
    {
        // do nothing.
    }

    /**
     * Returns the names of variables of this object that should be serialized.
     *
     * @return array Always returns an empty array.
     */
    public function __sleep()
    {
        return is_callable(['parent', '__sleep']) ? parent::__sleep() : [];
    }

    /**
     * Reconstructs resources that this object may have when unserializing.
     *
     * @return Nil Always returns $this.
     */
    public function __wakeup()
    {
        return is_callable(['parent', '__wakeup']) ? parent::__wakeup() : new Nil();
    }

    /**
     * Returns data which should be serialized to JSON.
     *
     * @return \stdClass Always returns \stdClass.
     */
    public function jsonSerialize()
    {
        return new \stdClass();
    }

    /**
     * Returns the current element.
     *
     * @return Nil Always returns $this.
     */
    public function current()
    {
        return new Nil();
    }

    /**
     * Moves forward to next element.
     */
    public function next()
    {
        // do nothing.
    }

    /**
     * Returns the key of the current element.
     *
     * @return Nil Always returns $this.
     */
    public function key()
    {
        return new Nil();
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool Always returns false.
     */
    public function valid()
    {
        return false;
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind()
    {
        // do nothing.
    }

    /**
     * Returns whether an offset exists.
     *
     * @param mixed $offset The offset to check for.
     *
     * @return bool Always returns false.
     */
    public function offsetExists($offset)
    {
        return false;
    }

    /**
     * Retrieves the value of an offset.
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return Nil Always returns $this.
     */
    public function offsetGet($offset)
    {
        return new Nil();
    }

    /**
     * Sets a value to an offset.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     */
    public function offsetSet($offset, $value)
    {
        // do nothing.
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset)
    {
        // do nothing.
    }

    /**
     * Returns the number of the elements of an object.
     *
     * @return int Always returns 0.
     */
    public function count()
    {
        return 0;
    }
}