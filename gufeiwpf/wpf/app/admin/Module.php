<?php
namespace Wpf\App\Admin;
class Module implements \Phalcon\Mvc\ModuleDefinitionInterface{
    public function registerAutoloaders(\Phalcon\DiInterface $di=null){ 
        
        $loader = new \Phalcon\Loader();
        
        $loader->registerDirs(
            array(
                APP_PATH.'/admin/Controllers/',
                APP_PATH.'/admin/Models/',
                //LIBS_PATH."/",
            )
        ,true);

        $loader->registerNamespaces(
            array(
                'Wpf\App\Admin\Controllers' => APP_PATH.'/admin/Controllers/',
                'Wpf\App\Admin\Models'      => APP_PATH.'/admin/Models/',
                'Wpf\App\Admin\Common'      => APP_PATH.'/admin/Common/',
            )
        ,true);

        $loader->register();
        
    }
    
    public function registerServices(\Phalcon\DiInterface $di){
        $di->set('view', function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(APP_PATH.'/admin/Views/');
            return $view;
        });
    }
}

if(file_exists(APP_PATH."/admin/Functions/functions.php")){
    require_once(APP_PATH."/admin/Functions/functions.php");
}