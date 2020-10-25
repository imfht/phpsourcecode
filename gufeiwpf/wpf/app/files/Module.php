<?php
namespace Wpf\app\files;
class Module implements \Phalcon\Mvc\ModuleDefinitionInterface{
    public function registerAutoloaders(\Phalcon\DiInterface $di=null){ 
        
        $loader = new \Phalcon\Loader();
        
        $loader->registerDirs(
            array(
                APP_PATH.'/files/Controllers/',
                APP_PATH.'/files/Models/',
                //LIBS_PATH."/",
            )
        ,true);

        $loader->registerNamespaces(
            array(
                'Wpf\App\Files\Controllers' => APP_PATH.'/files/Controllers/',
                'Wpf\App\Files\Models'      => APP_PATH.'/files/Models/',
                'Wpf\App\Files\Common'      => APP_PATH.'/files/Common/',
            )
        ,true);

        $loader->register();
        
    }
    
    public function registerServices(\Phalcon\DiInterface $di){
        $di->set('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(APP_PATH.'/files/Views/');
            return $view;
        });
    }
}
if(file_exists(APP_PATH."/files/Functions/functions.php")){
    require_once(APP_PATH."/files/Functions/functions.php");
}