<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;

use Closure;
use ReflectionClass;
use Timo\Exception\CoreException;

class Container
{
    /**
     * 容器绑定的服务
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * 共享的服务实例
     *
     * @var array
     */
    protected $instances = [];

    /**
     * 服务别名
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * 判断是否设置了该别名
     *
     * @param  string  $name
     * @return bool
     */
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * 注册一个服务到容器
     *
     * @param string $abstract 服务名称
     * @param null $concrete 具体的类名（包括命名空间）
     * @param bool $shared
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        $abstract = $this->normalize($abstract);

        $concrete = $this->normalize($concrete);

        if (is_array($abstract)) {
            list($abstract, $alias) = $this->extractAlias($abstract);

            $this->alias($abstract, $alias);
        }

        $this->dropStaleInstances($abstract);

        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * 注册一个共享服务到容器
     *
     * @param $abstract
     * @param null $concrete
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * 将一个已存在的服务实例作为共享服务注册到容器
     *
     * @param string $abstract
     * @param $instance
     */
    public function instance($abstract, $instance)
    {
        $abstract = $this->normalize($abstract);

        if (is_array($abstract)) {
            list($abstract, $alias) = $this->extractAlias($abstract);

            $this->alias($abstract, $alias);
        }

        unset($this->aliases[$abstract]);
        $this->instances[$abstract] = $instance;
    }

    /**
     * 为服务设置别名
     *
     * @param string $abstract
     * @param string $alias
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $this->normalize($abstract);
    }

    /**
     * 获取给定类型服务
     *
     * @param $abstract
     * @param array $params
     * @return mixed|object
     */
    public function get($abstract, array $params = [])
    {
        $abstract = $this->getAlias($this->normalize($abstract));

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        $object = $this->build($concrete, $params);

        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * 获取给定类型服务实例
     *
     * @param string $concrete
     * @param array $params
     * @return object
     * @throws CoreException
     */
    public function build($concrete, array $params = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $params);
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new CoreException("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $reflector->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters, $params);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * 解析依赖关系
     *
     * @param array $parameters
     * @param array $params
     * @return array
     * @throws CoreException
     */
    protected function getDependencies(array $parameters, $params)
    {
        $dependencies = [];

        /**
         * @var $parameter \ReflectionParameter
         */
        foreach ($parameters as $key => $parameter) {
            $dependency = $parameter->getClass(); //return ReflectionClass Object
            if ($dependency === null) {
                if (!empty($params)) {
                    $dependencies[] = array_shift($params);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();//ReflectionParameter::getDefaultValue — Gets default parameter value
                } else {
                    throw new CoreException("Can not be resolve class dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->get($dependency->name);//递归调用
            }
        }

        return $dependencies;
    }

    /**
     * Get the concrete type for a given abstract.
     *
     * @param  string  $abstract
     * @return mixed   $concrete
     */
    protected function getConcrete($abstract)
    {
        if (! isset($this->bindings[$abstract])) {
            return $abstract;
        }

        return $this->bindings[$abstract]['concrete'];
    }

    /**
     * 通过别名获取服务名称
     *
     * @param  string  $abstract
     * @return string
     */
    protected function getAlias($abstract)
    {
        return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }

    /**
     * Determine if a given type is shared.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        $abstract = $this->normalize($abstract);

        if (! isset($this->bindings[$abstract]['shared'])) {
            return false;
        }

        return $this->bindings[$abstract]['shared'] === true;
    }

    /**
     * 是否绑定了该服务
     *
     * @param $abstract
     * @return bool
     */
    public function has($abstract)
    {
        $abstract = $this->normalize($abstract);
        return isset($this->bindings[$abstract]);
    }

    /**
     * Extract the type and alias from a given definition.
     *
     * @param  array  $definition
     * @return array
     */
    protected function extractAlias(array $definition)
    {
        return [key($definition), current($definition)];
    }

    /**
     * Normalize the given class name by removing leading slashes.
     *
     * @param  mixed  $service
     * @return mixed
     */
    protected function normalize($service)
    {
        return is_string($service) ? ltrim($service, '\\') : $service;
    }
}
