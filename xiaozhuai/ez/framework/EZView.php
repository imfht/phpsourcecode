<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午7:56
 */
class EZView
{
    public $path;
    public $vars;

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZView();
        }
        return self::$instance;
    }

    function __construct(){
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

    public function setPath($p){
        $this->path = realpath($p);
    }

    public function render(){
        if(!file_exists($this->path) || !is_file($this->path)){
            EZErr::err(500, "view " . $this->path . " not exists");
        }
        try{
            $method = EZ()->getViewEngineMethod(EZConfig()->VIEW_ENGINE);
            if($method==null){
                EZErr::err(500, "view engine ".EZConfig()->VIEW_ENGINE." not register");
            }else{
                $method(EZGlobal()->VIEW_ENGINE_INSTANCE, $this->vars, $this->path);
            }
        }catch (Exception $e){
            EZErr::errException(500, $e);
        }

    }

}