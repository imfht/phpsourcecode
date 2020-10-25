<?php
namespace workerbase\classs;

/**
 * 业务容器，用于承载和验证业务模块类
 * @author fukaiyao
 */

class ServiceContainer
{
    /**
     * 业务对象
     * @var null
     */
    private $_srv = null;

    /**
     *  反射类
     * @var null
     */
    private $_reflectionClass = null;


    /**
     * ServiceContainer constructor.
     * @param object|string $srv 业务对象
     * @param array $arguments 参数
     * @throws \Exception
     * @throws \ReflectionException
     */
    function __construct($srv, $arguments = []){
        if ($srv instanceof \ReflectionClass) {
            $this->_reflectionClass = $srv;
            $this->_srv = $srv->newInstanceArgs($arguments);
        }
        elseif (is_object($srv)) {
            $this->_reflectionClass = new \ReflectionClass($srv);
            $this->_srv = $srv;
        }
        elseif (is_string($srv)) {
            if (!class_exists($srv)) {
                throw new \Exception('Class ' . $srv . ' is not exist!');
            }

            $this->_reflectionClass = new \ReflectionClass($srv);//反射类
            $this->_srv = $this->_reflectionClass->newInstanceArgs($arguments);
        }
        unset($srv, $arguments);
    }

    /**
     * 实例化方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参数
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        try {
            //前置方法是否存在
            if ($this->_reflectionClass->hasMethod('beforeAction')) {
               $res = call_user_func_array([$this->_srv, 'beforeAction'], [$args]);
               if (!is_null($res) && false === $res) {
                   return false;
               }
            }

            //过滤器数组
            if ($this->_reflectionClass->hasMethod('filters')) {
                $filters = call_user_func_array([$this->_srv, 'filters'], []);
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
                        $res = $instance->preFilter($args);
                        unset($instance);
                        if (!is_null($res) && false === $res) {
                            return false;
                        }
                    }
                }
            }

            //调用业务方法
            $result = call_user_func_array([$this->_srv, $method], $args);

            //后置方法是否存在
            if ($this->_reflectionClass->hasMethod('afterAction')) {
                $res = call_user_func_array([$this->_srv, 'afterAction'], [$result]);
                if (!is_null($res) && false === $res) {
                    return false;
                }
            }

            return $result;
        }
        catch (\Throwable $e) {
            Log::error($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 获取属性值时调用
     * @param string $propertyName 属性名
     *
     * @return int
     */
    public function __get($propertyName)
    {
       return $this->_srv->$propertyName;
    }

}

