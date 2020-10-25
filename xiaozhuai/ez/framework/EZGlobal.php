<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/14
 * Time: 下午6:47
 */
class EZGlobal
{
    private $vars;

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZGlobal();
        }
        return self::$instance;
    }

    function __construct()
    {
        $this->vars = array();
    }

    function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    function __get($name)
    {
        if(isset($this->vars[$name]))
            return $this->vars[$name];
        else
            return null;
    }

    function __isset($name)
    {
        return isset($this->vars[$name]);
    }

    function __invoke(){
        return $this->vars;
    }

}