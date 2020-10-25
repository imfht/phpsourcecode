<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午3:56
 */
class EZRouter
{

    public $uri;
    public $uriPathList;
    public $controllerPath;
    public $controllerName;
    public $action;
    public $viewPath;
    public $customRules = array();

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZRouter();
        }
        return self::$instance;
    }

    private function getURI(){
        $tmpArr = explode("?", $_SERVER["REQUEST_URI"]);
        $uri = $tmpArr[0];
        EZPath::removeLastSlash($uri);
        if(EZConfig()->WEB_ROOT == "/"){
            $this->uri = $uri;
        }else{
            $this->uri = substr($uri, strlen(EZConfig()->WEB_ROOT));
        }
    }

    private function parseURI(){
        $this->uriPathList = array();
        $tmp = explode("/", $this->uri);
        for($i=0; $i<count($tmp); $i++){
            if($tmp[$i]!="") {
                array_push($this->uriPathList, $tmp[$i]);
            }
        }
    }

    public function rules($rules){
        $pattern = '/:\w+/';
        foreach ($rules as $key => $value){
            $key = '/' . ltrim($key, '/');
            $value = '/' . ltrim($value, '/');

            preg_match_all($pattern, $key, $matches);
            $varKeys = [];
            for($i=0; $i<count($matches[0]); $i++){
                array_push($varKeys, substr($matches[0][$i], 1));
            }

            $regex = preg_replace($pattern, "(.*)", $key);

            array_push($this->customRules, array(
                "path"      => ltrim($value),
                "regex"     => '/' . str_replace('/', '\/', $regex) . '/',
                "var_keys"  => $varKeys
            ));
        }
    }

    private function matchController(){
        /**
         * match custom rules
         */
        for($i=count($this->customRules)-1; $i>=0; $i--){
            $regex = $this->customRules[$i]["regex"];
            $path = $this->customRules[$i]["path"];
            $varKeys = $this->customRules[$i]["var_keys"];
            if(substr($this->uri, 0, strlen($path)) == $path){
                if(preg_match($regex, $this->uri, $matches)){
                    for($i=1; $i<count($matches); $i++){
                        $_GET[$varKeys[$i-1]] = $matches[$i];
                    }
                    $this->uri = $path;
                    $this->parseURI();
                }
                break;
            }
        }


        if(count($this->uriPathList) == 0){                                 // $this->uri == "/"

            $this->controllerName = "Index";
            $this->action = "index";
            $this->controllerPath = $this->matchControllerPath(             // match controller ignore case
                EZGlobal()->CONTROLLER_PATH,
                $this->controllerName
            );
            $this->viewPath = EZGlobal()->VIEW_PATH . "/" .  "index." . EZConfig()->VIEW_EXT;

        }else if(count($this->uriPathList) == 1){                           // list size == 1

            $this->controllerName = $this->uriPathList[count($this->uriPathList)-1];
            $this->action = "index";
            $this->controllerPath = $this->matchControllerPath(             //match controller ignore case
                EZGlobal()->CONTROLLER_PATH . $this->combineURI(0, -1),
                $this->controllerName
            );
            $this->viewPath = EZGlobal()->VIEW_PATH . $this->combineURI(0, -1) . "/" . strtolower($this->controllerName . EZConfig()->VIEW_ACTION_HYPHEN . $this->action . "." . EZConfig()->VIEW_EXT);

        }else{                                                              // list size > 1

            $this->controllerName = $this->uriPathList[count($this->uriPathList)-1];
            $this->action = "index";
            $this->controllerPath = $this->matchControllerPath(             //match controller ignore case
                EZGlobal()->CONTROLLER_PATH . $this->combineURI(0, -1),
                $this->controllerName
            );
            $this->viewPath = EZGlobal()->VIEW_PATH . $this->combineURI(0, -1) . "/" . strtolower($this->controllerName . EZConfig()->VIEW_ACTION_HYPHEN . $this->action . "." . EZConfig()->VIEW_EXT);

            if($this->controllerPath!=null){
                return;
            }

            $this->controllerName = $this->uriPathList[count($this->uriPathList)-2];
            $this->action = $this->uriPathList[count($this->uriPathList)-1];
            $this->controllerPath = $this->matchControllerPath(             //match controller ignore case
                EZGlobal()->CONTROLLER_PATH . $this->combineURI(0, -2),
                $this->controllerName
            );
            $this->viewPath = EZGlobal()->VIEW_PATH . $this->combineURI(0, -2) . "/" . strtolower($this->controllerName . EZConfig()->VIEW_ACTION_HYPHEN . $this->action . "." . EZConfig()->VIEW_EXT);

        }
    }

    public function runController(){
        $className = $this->controllerName . "Controller";

        if($this->controllerPath == null) {
            EZErr::err(404, "controller \"" . $this->controllerName . "\" not found");
        }

        require_once $this->controllerPath;

        $class = new ReflectionClass($className);

        if(!$class->hasMethod($this->action)){
            EZErr::err(404, "action \"" . $this->action . "\" not found in controller \"" . $this->controllerName . "\"");
        }

        $actionMethod = $class->getMethod($this->action);

        if(!$actionMethod->isPublic()){
            EZErr::err(403, "action \"" . $this->action . "\" is not public in controller \"" . $this->controllerName . "\"");
        }


        EZView()->setPath($this->viewPath);
        $controllerInstance = $class->newInstance();

        $actionMethod->invoke($controllerInstance);
    }

    private function matchControllerPath($baseDir, $controllerName){
        if(!is_dir($baseDir)) return null;
        $dir = dir($baseDir);
        while($file = $dir->read()) {
            if(preg_match('/\.php$/', strtolower($file)) > 0){
                $tmpClassName = substr($file, 0, -4);
                if(strtolower($tmpClassName) == strtolower($controllerName . "Controller")){
                    return $baseDir . "/" . $file;
                }
            }
        }
        return null;
    }

    private function combineURI($start, $end=0){
        if($end<=0) $end = count($this->uriPathList) + $end;
        if($end<$start) return "";
        $uri = "";
        for($i=$start; $i<$end; $i++){
            $uri = $uri . "/" . $this->uriPathList[$i];
        }
        return $uri;
    }

    public function route(){
        $this->getURI();
        $this->parseURI();
        $this->matchController();
        $this->runController();
    }


}