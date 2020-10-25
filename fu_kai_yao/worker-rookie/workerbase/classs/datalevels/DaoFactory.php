<?php
namespace workerbase\classs\datalevels;

/**
 * Dao工厂，主要用于创建数据访问对象
 * Class DaoFactory
 * @author fukaiyao
 */
class DaoFactory
{
    /**
     * Dao对象缓存
     *
     * @var array
     */
    private static $_dao = array();

    //根目录命名空间
    private static $baseNamespace = '';

    //dao业务模块命名空间
    private static $modulesNamespace = '\system\datalevels';

    /**
     * 加载类，getDao('DaoType/应用目录下对应类名','参数1','参数2'……'参数n')
     * 如:getDao(DaoType::COMMON_TEST_TEST);
     * @return mixed
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getDao()
    {
        $arguments = func_get_args();//获取传给函数的参数（数组）
        $name = array_shift($arguments);//弹出第一个参数，即类名
        if ($name == '') {
            throw new \Exception('class name is empty!');
        }

        if (stripos($name, '.') === false) { //根据DaoType命名规则是以“.”隔开的判断，Dao模块配置加载
            throw new \Exception('This loading mode is not supported!');
        }

        //解析类名
        $className = self::loadClass($name);

        if (isset(self::$_dao[$className])) {
            return self::$_dao[$className];
        }

        if (!class_exists($className)) {
            throw new \Exception('Class ' . $className . ' is not exist!');
        }

        $classInfo = new \ReflectionClass($className);//反射类
        self::$_dao[$name] = $classInfo->newInstanceArgs($arguments);//传入参数
        return self::$_dao[$name];
    }

    /**
     * 创建指定Dao对象
     * @param string $daoType     - DaoType定义的业务类型
     * @return string
     */
    private static function loadClass($daoType)
    {
        $className = self::$baseNamespace . self::$modulesNamespace . '\\' .strtr($daoType, '.', '\\');

        return $className;
    }
}

