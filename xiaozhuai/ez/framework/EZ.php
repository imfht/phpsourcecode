<?php
/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/14
 * Time: 下午6:54
 */

class EZ
{

    private $preRunFunc;

    private $viewEngineMethod = array();

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZ();
        }
        return self::$instance;
    }

    function __construct(){
        $this->registerViewEngine("php", function ($engine, $vars, $path){
            foreach ($vars as $key => $value)
                ${$key} = $value;
            require_once $path;
        });
        $this->registerViewEngine("smarty", function ($engine, $vars, $path){
            foreach ($vars as $key => $value)
                $engine->assign($key, $value);
            $engine->display($path);
        });
        $this->registerViewEngine("twig", function ($engine, $vars, $path){
            $path = substr($path, strlen(realpath(EZGlobal()->TWIG_FILESYSTEM)));
            echo $engine->render($path, $vars);
        });
        $this->registerViewEngine("haml_php", function ($engine, $vars, $path){
            $engine->display($path, $vars);
        });
        $this->registerViewEngine("haml_twig", function ($engine, $vars, $path){
            $path = substr($path, strlen(realpath(EZGlobal()->TWIG_FILESYSTEM)));
            echo $engine->render($path, $vars);
        });
    }

    public function init($projectPath){
        EZPath::removeLastSlash($projectPath);
        EZConfig()->PROJECT_PATH = $projectPath;
        spl_autoload_register(function ($class_name) {
            $this->findAndImportClass(EZGlobal()->MODEL_PATH, $class_name);
            $this->findAndImportClass(EZGlobal()->LIBRARY_PATH, $class_name);
        });
    }

    private function findAndImportClass($path, $class_name){
        if(file_exists($path . "/" . $class_name . '.php')){
            require_once $path . "/" . $class_name . '.php';
        }else if(file_exists($path . "/" . $class_name . '.class.php')){
            require_once $path . "/" . $class_name . '.class.php';
        }
    }

    public function session($sessionStart=false){
        if($sessionStart) session_start();
    }

    public function viewEngine(&$viewEngine){
        EZGlobal()->VIEW_ENGINE_INSTANCE = $viewEngine;
    }

    public function registerViewEngine($name, $method){
        if(!is_string($name)){
            EZErr::err(500, "register view engine, engine name must be string");
        }
        if(!is_callable($method)){
            EZErr::err(500, "register view engine, engine method must be a callable function");
        }
        $this->viewEngineMethod[$name] = $method;
    }

    public function getViewEngineMethod($name){
        return $this->viewEngineMethod[$name];
    }

    public function errView($errViewPath){
        if(file_exists($errViewPath) && is_file($errViewPath)){
            EZGlobal()->ERR_VIEW_PATH = $errViewPath;
        }else{
            EZLog::warnning("err view template \"%s\" not exists", $errViewPath);
        }
    }

    public function config(){
        EZConfig()->normalize();
        $counts = func_num_args();                                 //get arguments count
        $configs = func_get_args();                                //get all arguments
        for($i=0; $i<$counts; $i++){
            EZConfig()->overrideWith($configs[$i]);                //override default config
            EZConfig()->normalize();
        }
    }

    public function routeRules($rules){
        EZRouter()->rules($rules);
    }

    public function mvc($modelPath, $viewPath, $controllerPath){                    //set mvc path
        EZPath::removeLastSlash($modelPath);
        EZPath::removeLastSlash($viewPath);
        EZPath::removeLastSlash($controllerPath);
        if( !( file_exists($modelPath) && is_dir($modelPath) ) ){
            EZErr::err(500, "model path \"$modelPath\" not exist or is not dir");
        }
        if( !( file_exists($viewPath) && is_dir($viewPath) ) ){
            EZErr::err(500, "view path \"$viewPath\" not exist or is not dir");
        }
        if( !( file_exists($controllerPath) && is_dir($controllerPath) ) ){
            EZErr::err(500, "controller path \"$controllerPath\" not exist or is not dir");
        }
        EZGlobal()->MODEL_PATH      = $modelPath;
        EZGlobal()->VIEW_PATH       = $viewPath;
        EZGlobal()->CONTROLLER_PATH = $controllerPath;
    }

    public function library($libraryPath){
        EZPath::removeLastSlash($libraryPath);
        EZGlobal()->LIBRARY_PATH = $libraryPath;
        if( !( file_exists($libraryPath) && is_dir($libraryPath) ) ){
            EZErr::err(500, "library path \"$libraryPath\" not exist or is not dir");
        }
        $libraryDir = dir($libraryPath);
        while($file = $libraryDir->read()) {
            if(is_file($libraryPath . "/" . $file) && ($file!=".") && ($file!="..")) {
                require_once $libraryPath . "/" . $file;
            }
        }
        $libraryDir->close();
    }

    public function location($url){
        $staticUrl = EZConfig()->WEB_ROOT . '/' . $url;
        EZPath::removeDuplicateSlash($staticUrl);
        header("location: {$staticUrl}");
        exit;
    }

    public function preRun($func){
        $this->preRunFunc = $func;
    }

    public function run(){
        $preRunFunc = $this->preRunFunc;
        if($preRunFunc!=null) {
            if (is_callable($preRunFunc)) {
                $preRunFunc();
            } else {
                EZErr::err(500, "preRun is not a callable function");
            }
        }
        EZRouter()->route();
    }

}