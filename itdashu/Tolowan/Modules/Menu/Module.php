<?php
use Core\Config;
$di->getShared('eventsManager')->attach('entity:links', function ($event, $entity) {
    if ($entity->getEntityId() == 'configList' && $entity->contentModel == 'menu') {
        $links = $entity->getLinks();
        $links['addBlock'] = array(
            'href' => array(
                'for' => 'adminMenuLinkAdd',
                'id' => $entity->id,
            ),
            'data-target' => 'right_handle',
            'icon' => 'info',
            'name' => '添加链接',
        );
        $links['sortBlock'] = array(
            'href' => array(
                'for' => 'adminMenuLinkList',
                'id' => $entity->id,
            ),
            'data-target' => 'right_handle',
            'icon' => 'info',
            'name' => '链接列表',
        );
        $entity->setLinks($links);
    }
});
function menuData($name, $isAction = false)
{
    $data = Config::get('m.menu.menu' . ucfirst($name) . 'Data');
    if ($isAction) {
        $data = isAction($data);
    }
    return $data;
}
function menuHierarchy($name)
{
    return Config::get('m.menu.menu' . ucfirst($name) . 'Hierarchy');
}
function menuRender($name, $isAction = false)
{
    $data = Config::get('m.menu.menu' . ucfirst($name) . 'Data', array());
    $hierarchy = Config::get('m.menu.menu' . ucfirst($name) . 'Hierarchy', array());
    if ($isAction) {
        $data = isAction($data);
    }
    return array(
        '#templates' => array(
            'menu',
            'menu-' . $name,
        ),
        '#module' => 'menu',
        'data' => $data,
        'hierarchy' => $hierarchy,
    );
}
function isAction($data)
{
    $widget = array();
    global $di;
    $router = $di->getShared('router');
    $moduleName = $router->getModuleName();
    $controllerName = $router->getControllerName();
    $actionName = $router->getActionName();
    foreach ($data as $k => $d) {
        $data[$k]['active'] = false;
        $widget[$k] = 0;
        if (is_string($d['href'])) {
            if ($d['href'] == $di->getShared('request')->getURI()) {
                $widget[$k] = 10;
                break;
            }
        }
        if (is_array($d['href'])) {
            $pathInfo = $router->getRouteByName($d['href']['for']);
            if ($pathInfo) {
                $pathInfo = $pathInfo->getPaths();
                if ($pathInfo['module'] == $moduleName && $pathInfo['controller'] = $controllerName) {
                    if ($pathInfo['action'] == 'index') {
                        $widget[$k] += 1;
                    }
                    if (strpos($actionName, $d['href']['for'])) {
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
