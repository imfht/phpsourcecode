<?php
namespace Kernel;

use Kernel\Route;
use Kernel\Loader;

class App
{
    protected $className;
    protected $actionName;
    protected $queryParam;

    public function __construct()
    {
        $this->dispatch = (new Route())->dispatch();
        $this->parseHandle();
    }
    public function run()
    {
        //绑定方法参数
        $class = new $this->className();
        if(method_exists($class,'_middleware')){
            $result = $class->_middleware();
            if(!is_null($result)){
                return $this->response($result);
            }
        }
        $reflect = new \ReflectionMethod($class, $this->actionName);
        $args = $this->bindParam($reflect, $this->queryParam);
        $result = $reflect->invokeArgs($class, $args);

        return $this->response($result);

    }

    public function response($result)
    {
        if(is_string($result)){
            echo $result;
        }elseif(is_array($result)){
            echo json_encode($result);
        }else{
            return false;
        }
        return true;
    }

    public function parseHandle()
    {
        $param = $this->dispatch[2];
        list($class,$action) = explode('@', $this->dispatch[1]);

        $this->className = '\\App\\Controller\\'.$class;

        if(strpos($action,'?') !== false){
            $query = preg_replace('/^.+\?/U','',$action);
            parse_str($query,$queryParam);
            $this->queryParam = $param + $queryParam;
            $this->actionName = preg_replace('/\?.*$/U','',$action);
        }else{
            $this->queryParam = $param;
            $this->actionName = $action;
        }
    }
    public function bindParam($reflect, $vars=[])
    {
        if(!$vars || !$reflect->getNumberOfParameters()){
            return [];
        }
        $args=[];
        //获取参数数量
        $params = $reflect->getParameters();
        foreach ($params as $param) {
            $name  = $param->getName();
            $class = $param->getClass();
            if($class){
                $className = $class->getName();
                $args[] = Loader::singleton($className);
            }elseif(isset($vars[$name])){
                $args[] = $vars[$name];
            }
        }
        return $args;
    }
}