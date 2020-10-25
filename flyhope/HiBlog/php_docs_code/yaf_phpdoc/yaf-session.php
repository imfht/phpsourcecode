<?php
/**
 * @package Yaf
 * @author 李枨煊<lcx165@gmail.com> (DOC Only)
 */
class Yaf_Session implements Iterator,Traversable,ArrayAccess,Countable {

    protected $_instance;

    protected $_session;

    protected $_started;

    /**
     * The offsetGet purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function offsetGet($name) {}

    /**
     * The __sleep purpose
     *
     * @return void
     */
    private function __sleep() {}

    /**
     * The rewind purpose
     *
     * @return void
     */
    public function rewind() {}

    /**
     * The del purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function del($name) {}

    /**
     * The key purpose
     *
     * @return void
     */
    public function key() {}

    /**
     * The has purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function has($name) {}

    /**
     * The valid purpose
     *
     * @return void
     */
    public function valid() {}

    /**
     * The count purpose
     *
     * @return void
     */
    public function count() {}

    /**
     * The start purpose
     *
     * @return void
     */
    public function start() {}

    /**
     * The offsetUnset purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function offsetUnset($name) {}

    /**
     * The offsetExists purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function offsetExists($name) {}

    /**
     * The next purpose
     *
     * @return void
     */
    public function next() {}

    /**
     * The getInstance purpose
     *
     * @return void
     */
    public static function getInstance() {}

    /**
     * The __wakeup purpose
     *
     * @return void
     */
    private function __wakeup() {}

    /**
     * The offsetSet purpose
     *
     * @param string $name 
     * @param string $value 
     *
     * @return void
     */
    public function offsetSet($name, $value) {}

    /**
     * The __isset purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function __isset($name) {}

    /**
     * The current purpose
     *
     * @return void
     */
    public function current() {}

    /**
     * The __clone purpose
     *
     * @return void
     */
    private function __clone() {}

    /**
     * The __set purpose
     *
     * @param string $name 
     * @param string $value 
     *
     * @return void
     */
    public function __set($name, $value) {}

    /**
     * The __construct purpose
     */
    function __construct() {}

    /**
     * The __unset purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function __unset($name) {}

    /**
     * The __get purpose
     *
     * @param string $name 
     *
     * @return void
     */
    public function __get($name) {}


}