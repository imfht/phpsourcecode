<?php
namespace workerbase\classs;

/**
 * 业务逻辑工厂，主要用于创建业务逻辑对象
 * Class ServiceFactory
 * @author fukaiyao
 */
class ServiceFactory
{
    /**
     * 业务对象缓存
     *
     * @var array
     */
    private static $_services = array();

    //根目录命名空间
    private static $baseNamespace = '';

    //services业务模块命名空间
    private static $modulesNamespace = '\system\services';

    /**
     * 加载类，getService('SrvType/应用目录下对应类名','参数1','参数2'……'参数n')
     * 如:getService(SrvType::COMMON_TEST_TEST);
     * @return mixed
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getService()
    {
        $arguments = func_get_args();//获取传给函数的参数（数组）
        $name = array_shift($arguments);//弹出第一个参数，即类名
        if ($name == '') {
            throw new \Exception('class name is empty!');
        }

        //加载类型：1业务模块service加载，2直接根据命名空间加载
        $loadType = 0;
        if (stripos($name, '.') !== false) { //根据SrvType命名规则是以“.”隔开的判断，业务模块配置加载
            $loadType = 1;
        }
        elseif (stripos($name, '\\') !== false) { //有命名空间的类名，直接根据命名空间加载
            //统一去除命名空间左边反斜杠
            if (substr($name, 0, 1) == '\\') {
                $name = ltrim($name, '\\');
            }
            $loadType = 2;
        } else {
            throw new \Exception('This loading mode is not supported!');
        }

        //解析类名
        if ($loadType == 1) {
            $className = self::loadClassFromService($name);
        }
        elseif ($loadType == 2) { //有命名空间，直接根据命名空间加载
            $className = self::loadClassFromNamespace($name);
        }

        if (isset(self::$_services[$className])) {
            return self::$_services[$className];
        }

        if (!class_exists($className)) {
            throw new \Exception('Class ' . $className . ' is not exist!');
        }

        $classInfo = new \ReflectionClass($className);//反射类
        self::$_services[$name] = new ServiceContainer($classInfo, $arguments);//传入参数
        unset($classInfo);
        return self::$_services[$name];
    }

    /**
     * 创建指定业务对象
     *
     * @param string $serviceType
     *            - SrvType定义的业务类型
     */
    private static function loadClassFromService($serviceType)
    {
        $className = self::$baseNamespace . self::$modulesNamespace . '\\' .strtr($serviceType, '.', '\\');

        return $className;
    }

    /**
     * 根据命名空间加载
     * @param $className
     * @return string
     * @throws \Exception
     */
    private static function loadClassFromNamespace($className)
    {
        $className = self::$baseNamespace . '\\' . $className;
        $className = strtr($className, DIRECTORY_SEPARATOR, '\\');

        return $className;
    }
}

