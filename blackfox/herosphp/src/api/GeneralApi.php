<?php
/**
 * 普通 api 网关
 * -------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2017-03-27 v2.0.0
 */
namespace herosphp\api;
use Exception;
use herosphp\bean\Beans;
use herosphp\core\Loader;
use herosphp\core\Log;
use herosphp\utils\JsonResult;
use ReflectionException;
use ReflectionMethod;

class GeneralApi
{

    /**
     * 调用服务
     */
    public static function run()
    {
        try {
            $instance = new self();
            $urlParams = $instance->_getUrlParams();
            $instance->_invoke($urlParams);
        } catch (APIException $e) {
            Log::error($e); //记录日志
            JsonResult::result($e->getCode(), $e->getMessage());
        }

    }

    /**
     * 服务调用
     * @param $urlParams array 访问url路径中提取的参数
     * @throws APIException
     */
    private function _invoke($urlParams)
    {
        $serviceClassPath = APP_NAME."\\api\\service\\".ucfirst($urlParams[0])."Service";
        try {
            $service = Loader::service($serviceClassPath);
        } catch(Exception $e) {
            throw new APIException(404, "Can not find the servive '{$serviceClassPath}'.");
        }
        $params = $_GET + $_POST; //获取参数

        //检查当前模块下是否有监听器，如果有则加载监听器
        $lisennerClassName = APP_NAME."\\api\\ModuleListener";
        $listener = null;
        try {
            $reflect = new \ReflectionClass($lisennerClassName);
            $listener = $reflect->newInstance();
        } catch (ReflectionException $exception) {
            //__print($exception);die();
        }
        // 这里做拦截和权限认证操作
        if ( $listener->needAuthrize("/{$urlParams[0]}/{$urlParams[1]}") ) {
            if ( !$listener->authorize($params) ) {
                throw new APIException(401, "Authorized Faild.");
            }
        }

        try {
            $reflectMethods = new ReflectionMethod($service, $urlParams[1]);
            $dependParams = array(); //依赖参数
            foreach ($reflectMethods->getParameters() as $value) {
                if (isset($params[$value->getName()])) { // 传入的参数的名称要跟服务方法的参数名相同
                    $dependParams[] = $params[$value->getName()];
                } else if ($value->isDefaultValueAvailable()) {
                    $dependParams[] = $value->getDefaultValue();
                } else {
                    $dependParams[] = null;
                }
            }
            $result = call_user_func_array(array($service, $urlParams[1]), $dependParams);
        } catch (Exception $e) {
            throw new APIException(500, $e->getMessage());
        }
        $result->output();  //输出json数据
    }

    /**
     * 解析URL，提取url参数
     */
    private function _getUrlParams()
    {
        $pathInfo = parse_url($_SERVER['REQUEST_URI']);
        $params = explode('/', trim($pathInfo['path'], '/'));
        if (count($params) != 2) {
            throw new APIException(404, 'Invalid resource path.');
        }
        return $params;
    }
} 