<?php
namespace Core\Mvc;

use Core\Config;
use Phalcon\Mvc\Router as Prouter;

class Router extends Prouter
{
    public function __construct($defaultRoutes = false)
    {
        parent::__construct($defaultRoutes);
        $this->removeExtraSlashes(true);
        $this->setDefaults(array(
            'namespace' => 'Modules\Core\Controllers',
            'module' => 'Core',
            'controller' => 'Index',
            'action' => 'Index',
        ));
        $this->notFound(array(
            'namespace' => 'Modules\Core\Controllers',
            'module' => 'Core',
            'controller' => 'Index',
            'action' => 'NotFound',
        ));
        // 加载路由和命名空间
        $routes = Config::cache('routes');
        foreach ($routes as $key => $route) {
            /**
            if ($key == 'index') {
            $this->add($route['pattern'], $route['paths'], $route['httpMethods'])->setName($key);
            $route['pattern'] = '/{language:([a-z]{2})}';
            //Config::printCode($route);
            $key = $key . 'Language';
            } else {
            if ($translate['translate'] && $translate['translate_type'] == 2) {
            $route['pattern'] = '/{language:([a-z]{2})}' . $route['pattern'];
            }
            }
             **/
            $this->add($route['pattern'], $route['paths'], $route['httpMethods'])->setName($key);
        }
    }
}
