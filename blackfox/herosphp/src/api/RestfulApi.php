<?php
///**
// * restful api 网关
// * -------------------------------------------------
// * @author yangjian<yangjian102621@gmail.com>
// * @since 2017-03-27 v2.0.0
// */
//namespace herosphp\api;
//
//use herosphp\core\Loader;
//use herosphp\core\Log;
//use herosphp\exception\HeroException;
//use herosphp\string\StringUtils;
//use herosphp\utils\JsonResult;
//
//class RestfulApi {
//
//    /**
//     * @var array 可用的 HTTP 动词
//     */
//    private static $_ALLOW_REQUEST_METHODS = array("GET", "POST", "DELETE", "PUT", "PATCH");
//
//    /**
//     * 调用服务
//     */
//    public static function run() {
//
//        try {
//
//            if ( !in_array($_SERVER['REQUEST_METHOD'], self::$_ALLOW_REQUEST_METHODS) ) {
//                throw new APIException(405, "Method '{$_SERVER['REQUEST_METHOD']}' is not allowed.");
//            }
//
//            $instance = new self();
//            $urlParams = $instance->_getUrlParams();
//            $instance->_invoke($urlParams);
//        } catch (HeroException $e) {
//            Log::error($e); //记录账号
//            JsonResult::result($e->getCode(), $e->getMessage());
//        }
//
//
//    }
//
//    /**
//     * 解析URL，提取url参数
//     * 目前只支持单层资源的访问，如 /zoos, /zoos/ID
//     * 暂时不支持多层资源访问，如 /zoos/ID/animals/ID
//     */
//    private function _getUrlParams() {
//        $pathInfo = parse_url($_SERVER['REQUEST_URI']);
//        $params = explode('/', trim($pathInfo['path'], '/'));
//        return $params;
//    }
//
//    /**
//     * 调用服务
//     * @param $urlParams
//     * @throws HeroException
//     */
//    private function _invoke($urlParams) {
//
//        $serviceClassPath = APP_NAME."\\api\\service\\".ucfirst($urlParams[0])."Service";
//        try {
//            $service = Loader::service($serviceClassPath);
//        } catch(\Exception $e) {
//            throw new APIException(404, "Can not find the servive '{$serviceClassPath}'.");
//        }
//
//        $__params = $this->_getBodyParams(); //获取参数
//        if ( $urlParams[1] ) {
//            $params['ID'] = $urlParams[1]; //注入ID
//        }
//
//        //检查当前模块下是否有监听器，如果有则加载监听器
//        $lisennerClassName = APP_NAME."\\api\\ModuleListener";
//        $listener = null;
//        try {
//            $reflect = new \ReflectionClass($lisennerClassName);
//            $listener = $reflect->newInstance();
//        } catch (\ReflectionException $exception) {
//            //__print($exception);die();
//        }
//        // 这里做拦截和权限认证操作
//        if ( $listener->needAuthrize("/{$urlParams[0]}/{$urlParams[1]}") ) {
//            if ( !$listener->authorize($params) ) {
//                throw new APIException(401, "Authorized Faild.");
//            }
//        }
//
//        //根据不同的 HTTP 动词找到对应的方法
//        switch ( $_SERVER['REQUEST_METHOD'] ) {
//            case "POST":
//                $method = 'add';
//                $params['data'] = $__params;
//                break;
//
//            case "PUT":
//            case "PATCH":
//                $method = 'update';
//                $params['data'] = $__params;
//                break;
//
//            case "DELETE":
//                $method = 'delete';
//                break;
//
//            default:
//                if ( $params['ID'] ) {
//                    $method = 'get';
//                } else {
//                    $method = 'gets';
//                }
//
//        }
//
//        try {
//            $reflectMethods = new \ReflectionMethod($service, $method);
//            $dependParams = array(); //依赖参数
//            foreach ($reflectMethods->getParameters() as $value) {
//                if (isset($params[$value->getName()])) {
//                    $dependParams[] = $params[$value->getName()];
//                } else if ($value->isDefaultValueAvailable()) {
//                    $dependParams[] = $value->getDefaultValue();
//                } else {
//                    $dependParams[] = null;
//                }
//            }
//
//            //调用服务
//            $result = call_user_func_array(array($service, $method), $dependParams);
//            $result->output();
//        } catch (\Exception $e) {
//            throw new APIException(500, $e->getMessage());
//        }
//    }
//
//    /**
//     * 获取请求body中的参数
//     */
//    private function _getBodyParams() {
//        $data = file_get_contents("php://input"); //获取输入流
//        return StringUtils::jsonDecode($data);
//    }
//}