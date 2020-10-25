<?php
namespace Alpaca;

class Router
{
    public $application = null;

    public $ModulePostfix = 'Module';

    public $ControllerPostfix = 'Controller';

    public $ActionPostfix = 'Action';

    public $DefaultModule = 'index';

    public $DefaultController = 'index';

    public $DefaultAction = 'index';

    public $Module = null;

    public $Controller = null;

    public $Action = null;

    public $ModuleName = null;

    public $ControllerName = null;

    public $ActionName = null;

    public $ModuleClassName = null;

    public $ControllerClassName = null;
    
    public $Params = Array();
    
    public $ControllerClass = null;
    
    public $ModuleClass = null;
    
    private $pathSegments = null;
    
    private static $instance;

    public function setAsGlobal()
    {
        self::$instance = $this;
        return $this;
    }

    public static function router()
    {
        return self::getInstance();
    }

    private static function getInstance()
    {
        if(!self::$instance){
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public function init()
    {                
        $request_url = $_SERVER['REQUEST_URI'];

        $this->forward($request_url);

        return $this;
    }

    public function forward($path)
    {        
        //处理请求路由路径，去掉参数
        $pos = stripos($path, '?');
        if ($pos) {
            $path = substr($path, 0, $pos);
        }
        
        //解析路由，生成Module、Controller、Action
        $parserResult = $this->parser($path);
        if($parserResult != true)
        {
            return null;
        }

        return $this;
    }

    //解析路由
    public function parser($path)
    {
        $segments = explode('/', $path);
 
        if (empty($segments[1])) {
            array_splice($segments, 1, 0, $this->DefaultModule);
        }
        
        if (empty($segments[2])) {
            array_splice($segments, 2, 0, $this->DefaultController);
        }

        if (empty($segments[3])) {
            array_splice($segments, 3, 0, $this->DefaultAction);
        }

        $this->Params = array_slice($segments, 4);
        
        if($this->pathSegments == $segments){
            echo "Endless Loop ! Do not redirect in the same action.";
            return false;
        }
        
        $this->pathSegments = $segments;

        // Module
        $this->Module = str_replace(array('.', '-', '_'), ' ', $segments[1]);
        $this->Module = ucwords($this->Module);
        $this->Module = str_replace(' ', '', $this->Module);              
        $this->ModuleName = $this->Module.$this->ModulePostfix;
        $this->ModuleClassName = $this->Module.'\\Module';
                               
        // Controller
        $this->Controller = str_replace(array('.', '-', '_'), ' ', $segments[2]);
        $this->Controller = ucwords($this->Controller);
        $this->Controller = str_replace(' ', '', $this->Controller);   
        $this->ControllerName = $this->Controller.$this->ControllerPostfix;
        $this->ControllerClassName = $this->Module.'\\Controller\\'.$this->ControllerName;
        
        // Action
        $this->Action = $segments[3];
        $this->Action = str_replace(array('.', '-', '_'), ' ', $segments[3]);
        $this->Action = ucwords($this->Action);
        $this->Action = str_replace(' ', '', $this->Action);
        $this->Action = lcfirst($this->Action);        
        $this->ActionName = $this->Action.$this->ActionPostfix;
        
        if(!in_array($this->Module,$this->application->getModules())){
            throw new \Exception("Module:$this->Module not found!");
        }
        
        if(!class_exists($this->ControllerClassName)){
            throw new \Exception("Controller:$this->ControllerClassName not found!");
        }
        
        if(!method_exists(new $this->ControllerClassName(), $this->ActionName))
        {
            throw new \Exception("Action:$this->ActionName not found!");
        }

        return $this;
    }

}
