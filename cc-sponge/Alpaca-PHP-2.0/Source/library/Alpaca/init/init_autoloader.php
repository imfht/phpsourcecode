<?php
spl_autoload_register(function($class){

    $config = Environment::env()->config();
    //有命名空间
    $className = str_replace("\\", "/", $class);
    //无命名空间
    $className = str_replace("_", "/", $className);

    //加载模块modules中的类
    if(!empty($config['application']['modules'])){
        $moduleNames = str_replace(",", "|", $config['application']['modules']);
    }else{
        $moduleNames = null;
    }
    if($moduleNames){
        $preg_moduleNames ="/(^({$moduleNames}))/";
        if(preg_match($preg_moduleNames,$className)){
            $className = APP_PATH . "/application/modules/".$className.".php";
            if(file_exists($className)){
                require_once ($className);
            }
            return;
        }
    }

    //加载Resources中的类
    if(!empty($config['application']['resource'])){
        $resourceNames = str_replace(",", "|", $config['application']['resource']);
    }else{
        $resourceNames = null;
    }
    $resourceNames=str_replace(",", "|", $config['application']['resource']);
    if($resourceNames){
        $preg_resourceNames ="/(^({$resourceNames}))/";
        if(preg_match($preg_resourceNames,$className)){
            $className =  APP_PATH . "/application/resource/".$className.".php";
            require_once($className);
            return;
        }
    }
            
    //加载Service中的类
    $serviceNames = 'Service';
    $preg_serviceNames ="/(^({$serviceNames}))/";
    if(preg_match($preg_serviceNames,$className)){       
        $className = lcfirst($className);        
        $className = APP_PATH . "/application/". $className.".php";
            require_once($className);
        return;
    }
    
    //加载library中的类
    if(!empty($config['application']['library'])){
        $resourceNames = str_replace(",", "|", $config['application']['library']);
    }else{
        $resourceNames = null;
    }
    $libraryNames = str_replace(",", "|", $config['application']['library']);
    if($libraryNames){
        $preg_libraryNames ="/(^({$libraryNames}))/";
        if(preg_match($preg_libraryNames,$className)){
            $className =  APP_PATH . "/library/".$className.".php";
            require_once($className);
            return;
        }
    }

});

//加载composer中的autoload
require_once(__DIR__."/../../vendor/autoload.php");
    
