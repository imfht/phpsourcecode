<?php
namespace Modules\Menu\Library;

use Core\Config;

class ViewTags{
    public static function menuData($name,$isAction = false){
        $data = Config::get('m.menu.menu'.ucfirst($name).'Data');
        if($isAction){
            $data = self::isAction($data);
        }
        return $data;
    }
    public static function menuHierarchy($name){
        return Config::get('m.menu.menu'.ucfirst($name).'Hierarchy');
    }
    public static function menuRender($name,$isAction = false){
        $data = Config::get('m.menu.menu'.ucfirst($name).'Data',array());
        $hierarchy = Config::get('m.menu.menu'.ucfirst($name).'Hierarchy',array());
        if($isAction){
            $data = self::isAction($data);
        }
        return array(
            '#templates' => array(
                'menu',
                'menu--'.$name
            ),
            '#module' => 'menu',
            'data' => $data,
            'hierarchy' => $hierarchy
        );
    }
    public static function isAction($data){
        $widget = array();
        global $di;
        $router = $di->getShared('router');
        $moduleName = $router->getModuleName();
        $controllerName = $router->getControllerName();
        $actionName = $router->getActionName();
        foreach($data as $k => $d){
            $data[$k]['active'] = false;
            $widget[$k] = 0;
            if(is_string($d['href'])){
                if($d['href'] == $di->getShared('request')->getURI()){
                    $widget[$k] = 10;
                    break;
                }
            }
            if(is_array($d['href'])){
                $pathInfo = $router->getRouteByName($d['href']['for']);
                if($pathInfo){
                    $pathInfo = $pathInfo->getPaths();
                    if($pathInfo['module'] == $moduleName && $pathInfo['controller'] = $controllerName){
                        if($pathInfo['action'] == 'index'){
                            $widget[$k] += 1;
                        }
                        if(strpos($actionName,$d['href']['for'])){
                            $widget[$k] += 2;
                        }
                    }
                }
            }
        }
        $active = array_search(max($widget), $widget);
        $data[$active]['active'] = true;
        return $data;
    }
}