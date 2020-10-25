<?php

/**
 * microAOP - 简洁而强大的AOP库
 *
 * @author      Dong Nan <hidongnan@gmail.com>
 * @copyright   (c) Dong Nan http://idongnan.cn All rights reserved.
 * @link        https://github.com/dongnan/microAOP/
 * @license     MIT ( http://mit-license.org/ )
 */

namespace microAOP;

/**
 * AOP 代理类
 *
 * @author DongNan <hidongnan@gmail.com>
 * @date 2015-9-18
 */
class Proxy {

    /**
     * 被代理类
     * @var object 
     */
    private $_mandator_;

    /**
     * 被代理类的类名
     * @var string 
     */
    private $_mandatorClassName_;

    /**
     * 已绑定的切面类的类名数组
     * @var array 
     */
    private $_aspects_ = [];

    /**
     * 已绑定的切面类的方法数组
     * @var array 
     */
    private $_aspectMethods_ = [];

    /**
     * 已绑定的函数数组
     * @var array 
     */
    private $_funcs_ = [];

    /**
     * 被代理类的方法的参数数组
     * @var array 
     */
    static private $_params_ = [];

    /**
     * 代理类构造方法
     * @param object $mandator  被代理类实例
     */
    private function __construct($mandator) {
        $this->_mandator_ = $mandator;
        $this->_mandatorClassName_ = get_class($mandator);
        if (!isset(self::$_params_[$this->_mandatorClassName_])) {
            self::$_params_[$this->_mandatorClassName_] = [];
        }
    }

    /**
     * 添加绑定切面类实例
     * @param string $name      切面类类名
     * @param object $aspect    切面类实例
     */
    private function _add_aspect_($name, $aspect) {
        if (!in_array($name, $this->_aspects_)) {
            $this->_aspects_[] = $name;
            $methods = get_class_methods($aspect);
            foreach ($methods as $method) {
                if (!isset($this->_aspectMethods_[$method])) {
                    $this->_aspectMethods_[$method] = [];
                }
                $this->_aspectMethods_[$method][$name] = $aspect;
            }
        }
    }

    /**
     * 添加绑定切面类实例数组
     * @param array $aspects    切面类实例的数组
     */
    private function _add_aspects_($aspects) {
        foreach ($aspects as $aspect) {
            if (is_object($aspect)) {
                $className = get_class($aspect);
                $this->_add_aspect_($className, $aspect);
            } elseif (is_string($aspect)) {
                if (class_exists($aspect)) {
                    $this->_add_aspect_($aspect, new $aspect());
                } else {
                    throw new \Exception("class '{$aspect}' is not found");
                }
            } else {
                throw new \Exception("type of the aspect '{$aspect}' is incorrect");
            }
        }
    }

    /**
     * 根据切面类类名移除已绑定的切面类实例
     * @param string $name 已绑定切面类实例的类名
     */
    private function _remove_aspect_($name) {
        if ($key = array_search($name, $this->_aspects_) !== false) {
            unset($this->_aspects_[$key]);
            foreach ($this->_aspectMethods_ as $method => &$objects) {
                unset($objects[$name]);
                if (empty($objects)) {
                    unset($this->_aspectMethods_[$method]);
                }
            }
        }
    }

    /**
     * 移除已绑定的切面类
     * @param array $aspects    切面类类名或实例的数组
     */
    private function _remove_aspects_($aspects) {
        foreach ($aspects as $aspect) {
            if (is_object($aspect)) {
                $className = get_class($aspect);
                $this->_remove_aspect_($className);
            } elseif (is_string($aspect)) {
                $this->_remove_aspect_($aspect);
            } else {
                throw new \Exception("type of the aspect '{$aspect}' is incorrect");
            }
        }
    }

    /**
     * 添加绑定函数，可以是函数、类的静态方法、对象的public方法或闭包
     * @param string $rules     触发执行绑定函数的被代理类的方法名或匹配方法名的规则
     * @param string $position  触发执行绑定函数的相对位置
     * @param array $funcs      需要绑定的函数集合
     */
    private function _add_funcs_($rules, $position, $funcs) {
        if (!isset($this->_funcs_[$rules])) {
            $this->_funcs_[$rules] = [];
        }
        if (!isset($this->_funcs_[$rules][$position])) {
            $this->_funcs_[$rules][$position] = [];
        }
        foreach ($funcs as $func) {
            if (is_callable($func)) {
                $this->_funcs_[$rules][$position][] = $func;
            }
        }
    }

    /**
     * 根据被代理类的方法名或匹配方法名的规则，移除指定绑定函数
     * @param string $rules     触发执行绑定函数的被代理类的方法名或匹配方法名的规则
     * @param string $position  触发执行绑定函数的相对位置
     */
    private function _remove_funcs_($rules, $position = null) {
        if (empty($position)) {
            if (isset($this->_funcs_[$rules])) {
                unset($this->_funcs_[$rules]);
            }
        } else {
            if (isset($this->_funcs_[$rules][$position])) {
                unset($this->_funcs_[$rules][$position]);
            }
        }
    }

    /**
     * 获取匹配触发规则的已绑定函数集合
     * @param array $funcs          已绑定的函数集合
     * @param string $methodName    当前调用的被代理类的方法的名称
     * @return array                匹配的已绑定的函数集合
     */
    static private function _get_match_funcs_($funcs, $methodName) {
        if (empty($funcs)) {
            return [];
        }
        $result = [];
        foreach ($funcs as $rules => $value) {
            if (preg_match("/^[A-Za-z]/", $rules[0])) {
                $rules = "/^{$rules}$/";
            }
            if (preg_match($rules, $methodName)) {
                foreach ($value as $position => $fns) {
                    if (!isset($result[$position])) {
                        $result[$position] = [];
                    }
                    $result[$position] = array_merge($result[$position], $fns);
                }
            }
        }
        return $result;
    }

    /**
     * 遍历地执行函数集合
     * @param array $funcs      函数集合
     * @param array $parameter  函数的参数
     */
    static private function _exec_funcs_($funcs, $parameter) {
        foreach ($funcs as $func) {
            call_user_func($func, $parameter);
        }
    }

    /**
     * 遍历地执行切面类实例指定方法
     * @param array $aspects        切面类实例
     * @param string $methodName    切面类的方法
     * @param array $parameter      切面类方法的参数
     */
    static private function _exec_aspects_($aspects, $methodName, $parameter) {
        foreach ($aspects as $aspect) {
            $aspect->$methodName($parameter);
        }
    }

    /**
     * 绑定切面类实例
     * @param object $mandator  被代理类的实例
     * @param mixed $_          待绑定的切面类实例，支持多个
     * @return boolean
     */
    static public function __bind__(&$mandator, $_ = null) {
        if (is_object($mandator)) {
            $args = func_get_args();
            array_shift($args);
            if (!($mandator instanceof self)) {
                $mandator = new self($mandator);
            }
            $mandator->_add_aspects_($args);
            return true;
        }
        return false;
    }

    /**
     * 根据切面类类名或实例移除已绑定的切面类实例
     * @param object $proxy 代理类实例
     * @param mixed $_      需要移除绑定的切面类类名或实例，支持多个
     * @return boolean
     */
    static public function __unbind__(&$proxy, $_ = null) {
        if ($proxy instanceof self) {
            $args = func_get_args();
            array_shift($args);
            $proxy->_remove_aspects_($args);
            return true;
        }
        return false;
    }

    /**
     * 绑定函数
     * @param object $mandator  被代理类实例
     * @param string $rules     触发执行绑定函数的被代理类的方法名或匹配方法名的规则，支持正则
     * @param string $position  触发执行绑定函数的相对位置，取值为:before,after,exception 或 always
     * @param mixed $_          需要绑定的函数
     * @return boolean
     */
    static public function __bind_func__(&$mandator, $rules, $position, $_ = null) {
        if (is_object($mandator) && !empty($rules)) {
            $args = func_get_args();
            array_splice($args, 0, 2);
            if (!($mandator instanceof self)) {
                $mandator = new self($mandator);
            }
            $mandator->_add_funcs_($rules, $position, $args);
            return true;
        }
        return false;
    }

    /**
     * 根据触发规则和位置移除已绑定的函数
     * @param self $proxy       代理类实例
     * @param string $rules     触发执行绑定函数的被代理类的方法名或匹配方法名的规则
     * @param string $position  触发执行绑定函数的相对位置，取值为:before,after,exception 或 always
     * @return boolean
     */
    static public function __unbind_func__(&$proxy, $rules, $position = null) {
        if ($proxy instanceof self && !empty($rules)) {
            $args = func_get_args();
            array_splice($args, 0, 2);
            $proxy->_remove_funcs_($rules, $position);
            return true;
        }
        return false;
    }

    public function __call($name, $arguments) {
        if (!is_object($this->_mandator_) || !method_exists($this->_mandator_, $name)) {
            throw new \Exception("the method '{$name}' doesn't exist in class {$this->_mandatorClassName_}");
        }
        $reflectionMethod = new \ReflectionMethod($this->_mandator_, $name);
        if (!$reflectionMethod->isPublic()) {
            throw new \Exception("the method '{$name}' cann't access public in class {$this->_mandatorClassName_}");
        }
        $args = [];
        if (isset(self::$_params_[$this->_mandatorClassName_][$name])) {
            foreach (self::$_params_[$this->_mandatorClassName_][$name] as $key => $param) {
                $args[$param['name']] = isset($arguments[$key]) ? $arguments[$key] : $param['default'];
            }
        } else {
            self::$_params_[$this->_mandatorClassName_][$name] = [];
            $parameters = $reflectionMethod->getParameters();
            foreach ($parameters as $key => $parameter) {
                $param = ['name' => $parameter->getName(), 'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null];
                self::$_params_[$this->_mandatorClassName_][$name][$key] = $param;
                $args[$param['name']] = isset($arguments[$key]) ? $arguments[$key] : $param['default'];
            }
        }

        $params = ['class' => $this->_mandatorClassName_, 'method' => $name, 'args' => $args];
        $funcs = self::_get_match_funcs_($this->_funcs_, $name);
        empty($this->_aspectMethods_["{$name}Before"]) || self::_exec_aspects_($this->_aspectMethods_["{$name}Before"], "{$name}Before", $params);
        empty($funcs['before']) || self::_exec_funcs_($funcs['before'], $params);
        try {
            $result = $reflectionMethod->invokeArgs($this->_mandator_, $arguments);
            $params['return'] = $result;
            empty($this->_aspectMethods_["{$name}After"]) || self::_exec_aspects_($this->_aspectMethods_["{$name}After"], "{$name}After", $params);
            empty($funcs['after']) || self::_exec_funcs_($funcs['after'], $params);
        } catch (\Exception $ex) {
            $params['exception'] = $ex;
            empty($this->_aspectMethods_["{$name}Exception"]) || self::_exec_aspects_($this->_aspectMethods_["{$name}Exception"], "{$name}Exception", $params);
            empty($funcs['exception']) || self::_exec_funcs_($funcs['exception'], $params);
        }
        empty($this->_aspectMethods_["{$name}Always"]) || self::_exec_aspects_($this->_aspectMethods_["{$name}Always"], "{$name}Always", $params);
        empty($funcs['always']) || self::_exec_funcs_($funcs['always'], $params);
        return isset($result) ? $result : null;
    }

    public function __set($name, $value) {
        if (!is_object($this->_mandator_)) {
            return false;
        }
        $this->_mandator_->$name = $value;
    }

    public function __get($name) {
        if (!is_object($this->_mandator_)) {
            return false;
        }
        return $this->_mandator_->$name;
    }

}
