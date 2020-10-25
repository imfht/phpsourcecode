<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/25
 * Time: 下午5:44
 */

namespace Inject;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ArrayAccess;
class Injector implements InjectorInterface,ArrayAccess
{

    const INDEX_CONCRETE = 0; //the concrete
    const INDEX_CACHED = 1; //cached it after produce
    protected $objects = []; //the objects not instantiated when map or closures
    protected $caches = []; //caches of the produced objects
    protected $data = []; //the objects instantiated when map
    protected $MUST_REG = false; //is MUST_REG is true,Inject won't produce the concrete unmapped

    /**
     * set MUST_REG
     */
    public function mustReg(){
        $this->MUST_REG = true;
    }

    /**
     * map an object that not instantiated
     * @param $key
     * @param $obj
     * @param bool $need_cache
     * @return $this
     */
    public function map($key,$obj = null,$need_cache = false){
        $this->clearCache($key);
        if (is_null($obj)) {
            $obj = $key;
        }
        $this->objects[$key] = [$obj,$need_cache];
        return $this;
    }

    /**
     * map an instantiated object
     * @param $key
     * @param $data
     */
    public function mapData($key,$data){
        $this->data[$key] = $data;
    }

    /**
     * map instantiated objects
     * @param $kvs
     */
    public function mapDatas($kvs){
        foreach ($kvs as $k => $v){
            $this->mapData($k,$v);
        }
    }

    /**
     * map an object that not instantiated and cache it after produce
     * @param $key
     * @param null $class
     * @return Injector
     */
    public function mapSingleton($key,$class = null){
        return $this->map($key,$class,true);
    }

    /**
     * map singletons
     * @param $kvs
     */
    public function mapSingletons($kvs){
        foreach ($kvs as $k => $v){
            $this->mapSingleton($k,$v);
        }
    }

    /**
     * get an object
     * @param $key
     * @return mixed
     * @throws InjectorException
     */
    public function get($key){
        if(isset($this->objects[$key])){
            return $this->objects[$key];
        }
        throw new InjectorException("obj $key not found");
    }

    /**
     * clear the cached objects
     * @param $key
     */
    public function clearCache($key){
        unset($this->caches[$key]);
    }

    /**
     * get an instantiated object mapped
     * @param $key
     * @return mixed
     * @throws InjectorException
     */
    public function getData($key){
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        throw new InjectorException("data $key not found");
    }

    /**
     * get a cached object
     * @param $key
     * @return mixed|null
     */
    public function getCache($key){
        return isset($this->caches[$key]) ? $this->caches[$key] : null;
    }

    /**
     * produce a concrete
     * @param $key
     * @param $params
     * @param bool $enable_reflect if $enable_reflect is true,it won't try to reflect for an unmapped concrete
     * @return mixed|object
     * @throws InjectorException
     */
    public function produce($key,$params = array(),$enable_reflect = true){
        //if in data
        if(isset($this->data[$key])) return $this->data[$key];
        //if cached
        if(isset($this->caches[$key])) return $this->caches[$key];
        //if obj/closure
        if(isset($this->objects[$key])){
            $obj = $this->get($key);
            $concrete = $obj[self::INDEX_CONCRETE];
        }else{
            if($this->MUST_REG || !$enable_reflect){
                throw new InjectorException("$key not registered");
            }else{
                $concrete = $key;
                $not_reg = true;
            }
        }
        $result = $this->build($concrete,$params);
        if($not_reg === true || $obj[self::INDEX_CACHED] === true){
            $this->caches[$key] = $result;
        }
        return $result;
    }

    /**
     * build concrete (a Closure or an object)
     * @param $concrete
     * @param array $params
     * @return object
     * @throws InjectorException
     */
    public function build($concrete,$params = array()){
        //if closure
        if($concrete instanceof Closure){
            return $this->call($concrete,$params);
//            return $concrete($this,$params);
        }
        //reflect
        $ref = new ReflectionClass($concrete);
        if(!$ref->isInstantiable()) throw new InjectorException("$concrete is not instantiable");
        $constructor = $ref->getConstructor();
        if(is_null($constructor)) return new $concrete;
        //constructor
        $params_in_constructor = $constructor->getParameters();
        $args = $this->apply($params_in_constructor,$params);
        return $ref->newInstanceArgs($args);
    }

    /**
     * fill the params(keys) by the values given and objects in Injector container
     * @param array $params
     * @param array $value_given
     * @return array
     * @throws InjectorException
     */
    public function apply(array $params,$value_given = array()){
        $result = array();
        foreach ($params as $param){
            if(key_exists($param->name,$value_given)){
                $result[] = $value_given[$param->name];
            }else{
                $class = $param->getClass();
                $name_to_produce = is_null($class) ? $param->name : $class->name;
                try{
                    $temp = $this->produce($name_to_produce,array(),false);
                }catch (InjectorException $e){
                    if($param->isDefaultValueAvailable()){
                        $temp = $param->getDefaultValue();
                    }else{
                        throw $e;
                    }
                }
                $result[] = $temp;
            }
        }
        return $result;
    }

    /**
     * call a closure
     * @param Closure $c
     * @param array $params
     * @return mixed
     */
    public function call(Closure $c,$params = array()){
        $ref = new ReflectionFunction($c);
        $params_need = $ref->getParameters();
        $args = $this->apply($params_need,$params);
        return call_user_func_array($c,$args);
        //return $ref->invokeArgs($args);
    }

    /**
     * call a method in class
     * @param $class_name
     * @param $action
     * @param array $params
     * @return mixed
     * @throws InjectorException
     */
    public function callInClass($class_name,$action,$params = array()){
        $ref = new ReflectionMethod($class_name, $action);
        if(!$ref->isPublic() && !$ref->isStatic()) throw new InjectorException("$class_name->$action is not public or static");
        $params_need = $ref->getParameters();
        $args = $this->apply($params_need,$params);
        $obj = $this->produce($class_name);
        return call_user_func_array([$obj,$action],$args);
        //return $ref->invokeArgs($obj,$args);
    }

    /**** implement methods of ArrayAccess ****/

    public function offsetExists($offset)
    {
        return isset($this->objects[$offset]) || isset($this->caches[$offset]) || isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->produce($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (! $value instanceof Closure){
            $value = function () use ($value) {
                return $value;
            };
        }
        $this->map($offset,$value);
    }

    public function offsetUnset($offset)
    {
        unset($this->objects[$offset],$this->caches[$offset],$this->data[$offset]);
    }

    public function flush(){
        $this->objects = [];
        $this->data = [];
        $this->caches = [];
    }
}