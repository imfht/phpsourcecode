<?php
namespace Wpf\app\cron;

class Module implements \Phalcon\Mvc\ModuleDefinitionInterface{
    public function registerAutoloaders(\Phalcon\DiInterface $di=null){ 
        $loader = new \Phalcon\Loader();
        
        $loader->registerDirs(
            array(
                APP_PATH.'/cron/Controllers/',
                APP_PATH.'/cron/Models/',
                //LIBS_PATH."/",
            )
        ,true);

        $loader->registerNamespaces(
            array(
                'Wpf\App\Cron\Controllers' => APP_PATH.'/cron/Controllers/',
                'Wpf\App\Cron\Models'      => APP_PATH.'/cron/Models/',
            )
        ,true);

        $loader->register();
        
    }
    
    public function registerServices(\Phalcon\DiInterface $di){
        $di->set('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(APP_PATH.'/cron/Views/');
            return $view;
        });
    }
}

if(file_exists(APP_PATH."/cron/Functions/functions.php")){
    require_once(APP_PATH."/cron/Functions/functions.php");
}
