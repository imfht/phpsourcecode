<?php
namespace Wpf\App\Www;
class Module implements \Phalcon\Mvc\ModuleDefinitionInterface{
    public function registerAutoloaders(\Phalcon\DiInterface $di=null){ 
        
        $loader = new \Phalcon\Loader();
        
        $loader->registerDirs(
            array(
                APP_PATH.'/www/controllers/',
                APP_PATH.'/www/models/',
                //LIBS_PATH."/",
            )
        ,true);

        $loader->registerNamespaces(
            array(
                'Wpf\App\Www\Controllers' => APP_PATH.'/www/Controllers/',
                'Wpf\App\Www\Models'      => APP_PATH.'/www/Models/',
                'Wpf\App\Www\Common'      => APP_PATH.'/www/Common/',
            )
        ,true);

        $loader->register();
        
    }
    
    public function registerServices(\Phalcon\DiInterface $di){
        $di->set('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(APP_PATH.'/www/Views/');
            return $view;
        });
    }
}

if(file_exists(APP_PATH."/www/Functions/functions.php")){
    require_once(APP_PATH."/www/Functions/functions.php");
}