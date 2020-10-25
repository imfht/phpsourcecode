<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 13:28
 */

namespace fastwork;


use Closure;
use fastwork\cache\Redis;
use fastwork\exception\ClassNotFoundException;
use ReflectionMethod;

/**
 * Class Container
 * @package fastwork
 * @property Config $config
 * @property Cookie $cookie
 * @property Env $env
 * @property Request $request
 * @property Response $response
 * @property Session $session
 * @property Validate $validate
 * @property Log $log
 * @property Route $route
 * @property Error $error
 * @property Redis $redis
 * @property Cache $cache
 * @property Db $db
 */
class Container implements \ArrayAccess, \Countable
{
    /**
     * 容器对象实例
     * @var Container
     */
    protected static $instance;

    /**
     * 容器中的对象实例
     * @var array
     */
    protected $instances = [];

    /**
     * 容器绑定标识
     * @var array
     */
    protected $bind = [
        'fastwork' => Fastwork::class,
        'config' => Config::class,
        'cookie' => Cookie::class,
        'env' => Env::class,
        'session' => Session::class,
        'validate' => Validate::class,
        'request' => Request::class,
        'response' => Response::class,
        'log' => Log::class,
        'route' => Route::class,
        'error' => Error::class,
        'redis' => Redis::class,
        'cache' => Cache::class,
        'db' => Db::class
    ];

    /**
     * 容器标识别名
     * @var array
     */
    protected $name = [];

    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 设置当前容器的实例
     * @access public
     * @param  object $instance
     * @return void
     */
    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * 获取容器中的对象实例
     * @access public
     * @param  string $abstract 类名或者标识
     * @param  array|true $vars 变量
     * @param  bool $newInstance 是否每次创建新的实例
     * @return object
     * @throws \ReflectionException
     * @throws ClassNotFoundException
     */
    public static function get($abstract, $vars = [], $newInstance = false)
    {
        return static::getInstance()->make($abstract, $vars, $newInstance);
    }

    /**
     * 绑定一个类、闭包、实例、接口实现到容器
     * @access public
     * @param  string $abstract 类标识、接口
     * @param  mixed $concrete 要绑定的类、闭包或者实例
     * @return Container
     */
    public static function set($abstract, $concrete = null)
    {
        return static::getInstance()->bindTo($abstract, $concrete);
    }

    /**
     * 绑定一个类实例当容器
     * @access public
     * @param  string $abstract 类名或者标识
     * @param  object $instance 类的实例
     * @return $this
     */
    public function instance($abstract, $instance)
    {
        if ($instance instanceof \Closure) {
            $this->bind[$abstract] = $instance;
        } else {
            if (isset($this->bind[$abstract])) {
                $abstract = $this->bind[$abstract];
            }

            $this->instances[$abstract] = $instance;
        }

        return $this;
    }

    /**
     * 判断容器中是否存在类及标识
     * @access public
     * @param  string $abstract 类名或者标识
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bind[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * 创建类的实例
     * @access public
     * @param  string $abstract 类名或者标识
     * @param  array|true $args 变量
     * @param  bool $newInstance 是否每次创建新的实例
     * @return object
     * @throws \ReflectionException
     * @throws ClassNotFoundException
     */
    public function make($abstract, $vars = [], $newInstance = false)
    {
        if (true === $vars) {
            // 总是创建新的实例化对象
            $newInstance = true;
            $vars = [];
        }

        $abstract = isset($this->name[$abstract]) ? $this->name[$abstract] : $abstract;

        if (isset($this->instances[$abstract]) && !$newInstance) {
            $object = $this->instances[$abstract];
        } else {
            if (isset($this->bind[$abstract])) {
                $concrete = $this->bind[$abstract];
                if ($concrete instanceof \Closure) {
                    $object = $this->invokeFunction($concrete, $vars);
                } else {
                    $this->name[$abstract] = $concrete;
                    $object = $this->make($concrete, $vars, $newInstance);
                }
            } else {
                $object = $this->invokeClass($abstract, $vars);
            }

            if (!$newInstance) {
                $this->instances[$abstract] = $object;
            }
        }

        return $object;
    }

    /**
     * 执行函数或者闭包方法 支持参数调用
     * @access public
     * @param  string|array|\Closure $function 函数或者闭包
     * @param  array $vars 变量
     * @return mixed
     * @throws \ReflectionException
     * @throws ClassNotFoundException
     */
    public function invokeFunction($function, $vars = [])
    {
        $reflect = new \ReflectionFunction($function);
        $args = $this->bindParams($reflect, $vars);

        return $reflect->invokeArgs($args);
    }

    /**
     * 调用反射执行类的方法 支持参数绑定
     * @access public
     * @param  string|array $method 方法
     * @param  array $vars 变量
     * @return mixed
     * @throws \ReflectionException
     * @throws ClassNotFoundException
     */
    public function invokeMethod($method, $vars = [])
    {
        if (is_array($method)) {
            $class = is_object($method[0]) ? $method[0] : $this->invokeClass($method[0]);
            $reflect = new \ReflectionMethod($class, $method[1]);
        } else {
            // 静态方法
            $reflect = new \ReflectionMethod($method);
        }

        $args = $this->bindParams($reflect, $vars);

        return $reflect->invokeArgs(isset($class) ? $class : null, $args);
    }

    /**
     * 调用反射执行callable 支持参数绑定
     * @access public
     * @param  mixed $callable
     * @param  array $vars 变量
     * @return mixed
     * @throws \ReflectionException
     * @throws ClassNotFoundException
     */
    public function invoke($callable, $vars = [])
    {
        if ($callable instanceof \Closure) {
            $result = $this->invokeFunction($callable, $vars);
        } else {
            $result = $this->invokeMethod($callable, $vars);
        }

        return $result;
    }

    /**
     * 调用反射执行类的实例化 支持依赖注入
     * @access public
     * @param  string $class 类名
     * @param  array $vars 变量
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function invokeClass($class, $vars = [])
    {
        try {
            $reflect = new \ReflectionClass($class);

            if ($reflect->hasMethod('__make')) {
                $method = new ReflectionMethod($class, '__make');

                if ($method->isPublic() && $method->isStatic()) {
                    $args = $this->bindParams($method, $vars);
                    return $method->invokeArgs(null, $args);
                }
            }
            $constructor = $reflect->getConstructor();

            if ($constructor) {
                $args = $this->bindParams($constructor, $vars);
            } else {
                $args = [];
            }

            return $reflect->newInstanceArgs($args);
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException('class not exists: ' . $class, $class);
        }
    }

    /**
     * 绑定参数
     * @access protected
     * @param  \ReflectionMethod|\ReflectionFunction $reflect 反射类
     * @param  array $vars 变量
     * @return array
     * @throws \ReflectionException
     * @throws ClassNotFoundException
     */
    public function bindParams($reflect, $vars = [])
    {
        $args = [];

        if ($reflect->getNumberOfParameters() > 0) {
            // 判断数组类型 数字数组时按顺序绑定参数
            reset($vars);
            $type = key($vars) === 0 ? 1 : 0;
            $params = $reflect->getParameters();

            foreach ($params as $param) {
                $name = $param->getName();
                $class = $param->getClass();

                if ($class) {
                    $className = $class->getName();
                    $args[] = $this->make($className);
                } elseif (1 == $type && !empty($vars)) {
                    $args[] = array_shift($vars);
                } elseif (0 == $type && isset($vars[$name])) {
                    $args[] = $vars[$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new \InvalidArgumentException('method param miss:' . $name);
                }
            }
        }

        return $args;
    }

    /**
     * 删除容器中的对象实例
     * @access public
     * @param  string|array $abstract 类名或者标识
     * @return void
     */
    public function delete($abstract)
    {
        foreach ((array)$abstract as $name) {
            $name = isset($this->name[$name]) ? $this->name[$name] : $name;

            if (isset($this->instances[$name])) {
                unset($this->instances[$name]);
            }
        }
    }

    /**
     * 绑定一个类、闭包、实例、接口实现到容器
     * @access public
     * @param  string|array $abstract 类标识、接口
     * @param  mixed $concrete 要绑定的类、闭包或者实例
     * @return $this
     */
    public function bindTo($abstract, $concrete = null)
    {
        if (is_array($abstract)) {
            $this->bind = array_merge($this->bind, $abstract);
        } elseif ($concrete instanceof Closure) {
            $this->bind[$abstract] = $concrete;
        } elseif (is_object($concrete)) {
            if (isset($this->bind[$abstract])) {
                $abstract = $this->bind[$abstract];
            }
            $this->instances[$abstract] = $concrete;
        } else {
            $this->bind[$abstract] = $concrete;
        }

        return $this;
    }

    public function __set($name, $value)
    {
        $this->bindTo($name, $value);
    }

    public function __get($name)
    {
        return $this->make($name);
    }

    public function __isset($name)
    {
        return $this->bound($name);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }

    public function offsetExists($key)
    {
        return $this->__isset($key);
    }

    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->__set($key, $value);
    }

    public function offsetUnset($key)
    {
        $this->__unset($key);
    }

    //Countable
    public function count()
    {
        return count($this->instances);
    }

    //IteratorAggregate
    public function getIterator()
    {
        return new ArrayIterator($this->instances);
    }

    public function __debugInfo()
    {
        $data = get_object_vars($this);
        unset($data['instances'], $data['instance']);

        return $data;
    }
}