<?php
namespace apps\api\pathinfo;

use apps\api\BaseController;
use workerbase\classs\Request;

/**
 * 自动搜索控制器
 * Class AutoPathController
 * @package apps\api\pathinfo
 */
class AutoPathController extends BaseController
{

    public function runController()
    {
        $route = Request::getInstance()->baseUrl();
        $tRoute = strtolower($route);

        if ($tRoute == '/pathinfo/autopath/runcontroller') {
            return $this->showResponse(-1, '非法访问！');
        }

        $tRoute = explode('/', $tRoute);
        $newRoute = [];
        foreach ($tRoute as $r) {
            if (empty($r)) {
                continue;
            }
            $newRoute[] = $r;
        }
        unset($tRoute);
        $len = count($newRoute);

        $urlSlice1 = array_pop($newRoute);
        $urlSlice2 = array_pop($newRoute);
        $newRoute = implode("\\", $newRoute);

        //检测最后一个url分片代表的控制器是否存在
        $controllerPath = "apps\\" . WK_APP_ID;
        $controllerPath .= !empty($newRoute) ? "\\" . $newRoute : "";
        if (!empty($urlSlice1)) {
            if (stripos($urlSlice2, '_') !== false) { //下划线转驼峰
                $urlSlice2 = explode('_', $urlSlice2);
                foreach ($urlSlice2 as &$slice) {
                    $slice = ucfirst($slice);
                }

                $urlSlice2 = implode('', $urlSlice2);
            }

            $controllerPath .= "\\" . $urlSlice2 . 'Controller';

            $class = strtr($controllerPath, DIRECTORY_SEPARATOR, '\\');
            if (!class_exists($class)) {
                return $this->showResponse(-1, '404:: ' . $class . ' Not Found！');
            }

            $controller = new $class();

            $filterRes = $this->_runFilters($controller,  Request::getInstance()->GPC());
            if (!$filterRes) {
                return '';
            }

            if(!method_exists($controller, $urlSlice1)) {
                return $this->showResponse(-1, '404:: ' . $urlSlice1 . ' action Not Found！');
            } else {
                call_user_func(array($controller, $urlSlice1));
            }
        }
        else { //默认控制器处理
            return $this->showResponse(-1, '地址错误!');
        }
    }


    private function _runFilters($callbacks, $params = [])
    {
        //过滤器数组
        if (method_exists($callbacks, 'filters')) {
            $filters = call_user_func_array([$callbacks, 'filters'], []);
            if ($filters && is_array($filters)) {
                foreach ($filters as $filter) {
                    if (!class_exists($filter)) {
                        continue;
                    }
                    $reflectionClass = new \ReflectionClass($filter);
                    if (!$reflectionClass->IsInstantiable()) { //是否可实例化
                        continue;
                    }
                    if (!$reflectionClass->hasMethod('init')) { //方法是否存在
                        continue;
                    }

                    if (!$reflectionClass->hasMethod('preFilter')) { //方法是否存在
                        continue;
                    }

                    unset($reflectionClass);
                    $instance = new $filter;
                    $instance->init();
                    $res = $instance->preFilter($params);
                    unset($instance);
                    if (empty($res)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}